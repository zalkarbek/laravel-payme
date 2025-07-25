<?php

namespace App\Http\Controllers\Payment;

use App\DTO\Paycom\Factories\PaycomDTOFactory;
use App\Exceptions\PaycomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Paycom\PaycomRequest;
use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaycomController extends Controller
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ) {
        parent::__construct();
    }

    /**
     * @param PaycomRequest $request
     * @param PaycomDTOFactory $paycomDTOFactory
     * @return JsonResponse
     */
    public function __invoke(PaycomRequest $request, PaycomDTOFactory $paycomDTOFactory): JsonResponse
    {
        $method = $request->rpcMethod();

        try {
            $className = $this->getServiceClassName($method);

            if (!class_exists($className) || !method_exists($className, 'run')) {
                throw new PaycomException(
                    $request->requestId(),
                    PaycomException::message(
                        "Метод не реализован",
                        "Usul amalga oshirilmagan",
                        "Method not implemented"
                    ),
                    PaycomException::ERROR_METHOD_NOT_FOUND,
                    $method
                );
            }

            $service = app($className);
            $result = $service->run($paycomDTOFactory->makeFromPaycomRequest($request));

            return response()->json([
                'result' => $result,
            ]);
        }
        catch (PaycomException $e) {
            report($e);
            return response()->json($e->responseError());
        }
        catch (\Exception $e) {
            PaycomException::manualReport($e, $request->requestId());

            $exception = new PaycomException(
                $request->requestId(),
                PaycomException::message(
                    "Внутренняя ошибка",
                    "Ichki xato",
                    "Internal error",
                ),
                PaycomException::ERROR_INTERNAL_SYSTEM,
            );
            return response()->json($exception->responseError());
        }
    }


    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function paymentResult(Request $request): Factory|View|Application|RedirectResponse
    {
        $paymentId = $request->get('payment_id');

        if (empty($paymentId)) {
            abort(404);
        }

        $isPaid = true;
        $payment = $this->paymentRepository->findPaidById($paymentId);

        if (empty($payment)) {
            $isPaid = false;
        }

        return view('pages.payment', [
            'payment' => $payment,
            'isPaid' => $isPaid,
        ]);
    }

    /**
     * @param string $method
     * @return string
     */
    private function getServiceClassName(string $method): string
    {
        return 'App\Services\Payments\Paycom\\'.$method.'Service';
    }
}

