<?php

namespace App\Http\Requests\Paycom;

use App\Http\Requests\Paycom\RpcRules\BaseRpcRules;
use App\Http\Requests\Paycom\RpcRules\RpcRulesInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaycomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'method' => (string) ($this->input('method') ?? ''),
        ]);
    }

    /**
     * @return class-string<RpcRulesInterface>|null
     */
    protected function resolveRulesClass(): ?string
    {
        $method = $this->rpcMethod();

        if (!$method) {
            return null;
        }

        $class = 'App\\Http\\Requests\\Paycom\\RpcRules\\' . ucfirst($method) . 'Rules';

        return class_exists($class) ? $class : null;
    }

    public function rules(): array
    {
        $class = $this->resolveRulesClass();

        if (!$class) {
            return BaseRpcRules::rules();
        }

        return $class::rules();
    }

    public function failedValidation(Validator $validator)
    {
        $rulesClass = $this->resolveRulesClass();

        if ($rulesClass && method_exists($rulesClass, 'handleValidationFailure')) {
            $exception = $rulesClass::handleValidationFailure($this->requestId(), $validator);
        } else {
            $exception = BaseRpcRules::handleValidationFailure($this->requestId(), $validator);
        }

        throw new HttpResponseException(response()->json($exception->responseError()));
    }

    public function rpcVersion()
    {
        return $this->input('jsonrpc');
    }

    public function requestId(): ?int
    {
        return $this->input('id');
    }

    public function rpcMethod(): ?string
    {
        return $this->input('method');
    }

    public function params(?string $key): mixed
    {
        if ($key === null) {
            return $this->input('params', []);
        }

        return $this->input("params.{$key}");
    }

    public function account(?string $key): mixed
    {
        if($key === null) {
            return $this->input('account');
        }

        return $this->input("params.account.$key");
    }
}
