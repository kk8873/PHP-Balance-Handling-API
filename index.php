<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Sample Data
$groups = [
    [
        "id" => 1,
        "name" => "Group 1",
        "members" => [
            ["id" => 1, "name" => "Aliya"],
            ["id" => 2, "name" => "Buhan"],
            ["id" => 3, "name" => "Cheeti"]
        ]
    ],
    [
        "id" => 2,
        "name" => "Group 2",
        "members" => [
            ["id" => 4, "name" => "Divesh"],
            ["id" => 5, "name" => "Ajay"],
            ["id" => 6, "name" => "Ram"]
        ]
    ],
    [
        "id" => 3,
        "name" => "Group 3",
        "members" => [
            ["id" => 7, "name" => "Harh"],
            ["id" => 8, "name" => "Karan"],
            ["id" => 9, "name" => "Raj"]
        ]
    ]
];

$expenses = [];

// Helper function to find a group by ID
function findGroupById($groupId, &$groups) {
    foreach ($groups as &$group) {
        if ($group['id'] == $groupId) {
            return $group;
        }
    }
    return null;
}

// Get Group Members
$app->get('/group/{group_id}/members', function (Request $request, Response $response, $args) use ($groups) {
    $groupId = (int) $args['group_id'];
    $group = findGroupById($groupId, $groups);
    
    if ($group) {
        return $response->withJson($group['members']);
    }
    
    return $response->withJson(["error" => "Group not found"], 404);
});

// Get Group Balances
$app->get('/group/{group_id}/balances', function (Request $request, Response $response, $args) use ($groups, &$expenses) {
    $groupId = (int) $args['group_id'];
    $group = findGroupById($groupId, $groups);
    
    if (!$group) {
        return $response->withJson(["error" => "Group not found"], 404);
    }

    $groupMembers = $group['members'];
    $numMembers = count($groupMembers);
    if ($numMembers == 0) {
        return $response->withJson(["message" => "Group has no members"], 200);
    }

    $balances = [];
    foreach ($groupMembers as $member) {
        $balances[$member['id']] = 0;
    }

    foreach ($expenses as $expense) {
        if ($expense['group_id'] == $groupId) {
            $amountPerMember = $expense['amount'] / $numMembers;
            $balances[$expense['paid_by']] -= $expense['amount'];
            foreach ($groupMembers as $member) {
                if ($member['id'] != $expense['paid_by']) {
                    $balances[$member['id']] += $amountPerMember;
                }
            }
        }
    }

    $balancesList = [];
    foreach ($balances as $memberId => $balance) {
        $memberName = array_column($groupMembers, 'name', 'id')[$memberId] ?? null;
        $balancesList[] = ["member_id" => $memberId, "member_name" => $memberName, "balance" => $balance];
    }

    return $response->withJson($balancesList);
});

// Record a Payment
$app->post('/payment', function (Request $request, Response $response) use (&$expenses, $groups) {
    $data = $request->getParsedBody();
    $from_member = $data['from_member'] ?? null;
    $to_member = $data['to_member'] ?? null;
    $amount = $data['amount'] ?? null;
    $group_id = $data['group_id'] ?? null;

    if (!$from_member || !$to_member || !$amount || !$group_id) {
        return $response->withJson(["error" => "Invalid input. Missing required fields."], 400);
    }

    $group = findGroupById($group_id, $groups);
    if (!$group) {
        return $response->withJson(["error" => "Group not found"], 404);
    }

    $expenses[] = [
        "group_id" => $group_id,
        "paid_by" => $from_member,
        "amount" => $amount
    ];

    return $response->withJson(["message" => "Payment recorded successfully."], 201);
});

// Create an Expense
$app->post('/expense', function (Request $request, Response $response) use (&$expenses, $groups) {
    $data = $request->getParsedBody();
    $group_id = $data['group_id'] ?? null;
    $paid_by = $data['paid_by'] ?? null;
    $amount = $data['amount'] ?? null;

    if (!$group_id || !$paid_by || !$amount) {
        return $response->withJson(["error" => "Invalid input. Missing required fields."], 400);
    }

    $group = findGroupById($group_id, $groups);
    if (!$group) {
        return $response->withJson(["error" => "Group not found"], 404);
    }

    if (!in_array($paid_by, array_column($group['members'], 'id'))) {
        return $response->withJson(["error" => "Member not found in group"], 400);
    }

    $expenses[] = [
        "group_id" => $group_id,
        "paid_by" => $paid_by,
        "amount" => $amount
    ];

    return $response->withJson(["message" => "Expense recorded successfully."], 201);
});

// Update Group Balances
$app->post('/group/{group_id}/balances', function (Request $request, Response $response, $args) use ($groups) {
    $groupId = (int) $args['group_id'];
    $data = $request->getParsedBody();
    $balances = $data['balances'] ?? null;

    if (!$balances) {
        return $response->withJson(["error" => "Balances data is required"], 400);
    }

    return $response->withJson(["message" => "Balances updated successfully.", "balances" => $balances], 200);
});

// Update Due Payments
$app->post('/group/{group_id}/due_payments', function (Request $request, Response $response, $args) use ($groups) {
    $groupId = (int) $args['group_id'];
    $data = $request->getParsedBody();
    $payments = $data['payments'] ?? null;

    if (!$payments) {
        return $response->withJson(["error" => "Payments data is required"], 400);
    }

    return $response->withJson(["message" => "Due payments updated successfully.", "payments" => $payments], 200);
});

// Run the App
$app->run();
