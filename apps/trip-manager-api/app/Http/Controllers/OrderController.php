<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Infra\Repositories\DestinationRepository;
use App\Infra\Repositories\UserRepository;
use App\Infra\Repositories\OrderRepository;
use App\Infra\Services\OrderService;
use DateTimeImmutable;
use Domain\Destination\Exceptions\DestinationNotFoundException;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Exceptions\ApproveException;
use Domain\Order\Exceptions\CancelException;
use Domain\Order\Exceptions\InvalidDatesException;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\UseCases\Index\Index;
use Domain\Order\UseCases\Index\IndexDTO;
use Domain\Order\UseCases\Show\Show;
use Domain\Order\UseCases\Update\UpdateDTO;
use Domain\Order\UseCases\Update\UpdateDetails;
use Domain\Order\UseCases\UpdateStatus\UpdateStatus;
use Domain\Order\UseCases\UpdateStatus\UpdateStatusDTO;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\Order\UseCases\Create\Create;
use Domain\Order\UseCases\Create\CreateDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:APPROVED,CANCELED,REQUESTED',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'destination_id' => 'nullable|integer',
        ]);

        $listDTO = new IndexDTO(
            userAuthorId: auth()->id(),
            status: $request->status ? OrderStatus::from($request->status) : null,
            startDate: $request->start_date ? new DateTimeImmutable($request->start_date) : null,
            endDate: $request->end_date ? new DateTimeImmutable($request->end_date) : null,
            destinationId: $request->destination_id
        );

        $useCase = new Index(
            orderRepository: new OrderRepository(),
            userRepository: new UserRepository(),
        );

        try {
            $orders = $useCase->execute($listDTO);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }

        return response()->json(OrderResource::list($orders->orders()), 200);
    }

    /**
     * Create a new order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'destination_id' => 'required|integer',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
        ]);

        $createDTO = new CreateDTO(
            userId: auth()->id(),
            destinationId: $request->destination_id,
            departureDate: new DateTimeImmutable($request->departure_date),
            returnDate:  new DateTimeImmutable($request->return_date),
        );

        $useCase = new Create(
            orderRepository: new OrderRepository(),
            userRepository: new UserRepository(),
            destinationRepository: new DestinationRepository()
        );

        try {
            $useCase->execute($createDTO);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (UserNotAuthorized $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 403);
        } catch (DestinationNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }
        return response()->json([], 204);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $useCase = new Show(
            orderRepository: new OrderRepository(),
            userRepository: new UserRepository()
        );

        try {
            $order = $useCase->execute($id, auth()->id());
        } catch (OrderNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (UserNotAuthorized $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }

        return response()->json(OrderResource::make($order));
    }

    /**
     * Update the specified order details.
     *
     * @param Request $request
     * @param int $orderid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $orderid): JsonResponse
    {
        $request->validate([
            'destination_id' => 'required|integer',
            'departure_date' => 'required|date',
            'return_date' => 'required|date',
        ]);

        $updateDTO = new UpdateDTO(
            orderId: $orderid,
            userAuthorId: auth()->id(),
            destinationId: $request->destination_id,
            departureDate: new DateTimeImmutable($request->departure_date),
            returnDate: new DateTimeImmutable($request->return_date)
        );

        $useCase = new UpdateDetails(
            orderRepository: new OrderRepository(),
            userRepository: new UserRepository(),
            destinationRepository: new DestinationRepository()
        );

        try {
            $useCase->execute($updateDTO);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (UserNotAuthorized $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 403);
        } catch (DestinationNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (OrderNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        } catch (InvalidDatesException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }

        return response()->json([], 204);
    }

    /**
     * Update the status of the specified order.
     *
     * @param Request $request
     * @param int $orderid
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, int $orderid): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:APPROVED,CANCELED',
        ]);

        $dto = new UpdateStatusDTO(
            orderId: $orderid,
            userAuthorId: auth()->id(),
            status: OrderStatus::from($request->status)
        );

        $useCase = new UpdateStatus(
            orderRepository: new OrderRepository(),
            userRepository: new UserRepository(),
            destinationRepository: new DestinationRepository(),
            orderService: new OrderService()
        );

        try {
            $useCase->execute($dto);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (UserNotAuthorized $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 403);
        } catch (OrderNotFoundException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        } catch (ApproveException $e) {
            return response()->json([], 204);
        } catch (CancelException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }

        return response()->json([], 204);
    }
}
