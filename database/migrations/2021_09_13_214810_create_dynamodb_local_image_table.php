<?php

use Illuminate\Database\Migrations\Migration;
use BaoPham\DynamoDb\DynamoDbClientService;

class CreateDynamodbLocalImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ローカル以外の時には実行しない
        if (!(env('DYNAMODB_CONNECTION') === 'local')) {
            return;
        }
        $dynamoDbClientService = resolve(DynamoDbClientService::class);
        $client = $dynamoDbClientService->getClient();

        $params = [
            'TableName' => 'Lemonade-images',
            'KeySchema' => [
                [
                    'AttributeName' => 'id',
                    'KeyType' => 'HASH',
                ]
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'id',
                    'AttributeType' => 'S'
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 2,
                'WriteCapacityUnits' => 1,
            ],
        ];

        $client->createTable($params);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ローカル以外の時には実行しない
        if (!(env('DYNAMODB_CONNECTION') === 'local')) {
            return;
        }
        $dynamoDbClientService = resolve(DynamoDbClientService::class);
        $client = $dynamoDbClientService->getClient();

        $params = [
            'TableName' => 'Lemonade-images',
        ];

        $client->deleteTable($params);
    }
}
