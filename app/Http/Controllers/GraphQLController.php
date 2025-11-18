<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Events\Dispatcher as EvenetDispatcher;
use Illuminate\Http\Request;
use Laragraph\Utils\RequestParser;
use Nuwave\Lighthouse\Events\EndRequest;
use Nuwave\Lighthouse\Events\StartRequest;
use Nuwave\Lighthouse\GraphQL;
use Nuwave\Lighthouse\Support\Contracts\CreatesContext;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class GraphQLController extends Controller
{
    public function __invoke(Request $request, GraphQL $graphQL, EventDispatcher $eventsDispatcher, RequestParser $requestParser, CreatesResponse $createsResponse, CreatesContext $createsContext): Response
    {
        // Dispatch event indicating the start of the request
        $eventsDispatcher->dispatch(new StartRequest($request));

        // Extract GraphQL parameters from the request
        $graphQLRequestOperation = $request->get('operation');
        $graphQLRequestQuery = $request->get('query');
        $graphQLRequestOperationName = $request->get('operationName');

        // Parse the raw request into a structured GraphQL operation
        $graphQLOperation = $requestParser->parseRequest($request);
        $graphQLContext = $createsContext->generate($request);
        $graphQLResult = $graphQL->executeOperationOrOperations($graphQLOperation, $graphQLContext);

        // Store the GraphQL execution result
        $result = $graphQLResult;

        // Create an HTTP response from the GraphQL result
        $response = $createsResponse->createResponse($result);
        $eventsDispatcher->dispatch(new EndRequest($response));

        return $response;
    }
}
