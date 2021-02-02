<?php

namespace App\ArgumentResolver;


use App\TransferObjects\Request\RequestTransferInterface;
use Doctrine\Common\Proxy\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ArgumentRequestValueResolver implements ArgumentValueResolverInterface
{
    private const FORMAT = 'json';

    private $serializer;

    private $validator;


    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator  = $validator;
    }


    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return \is_a($argument->getType(), RequestTransferInterface::class, true);
    }


    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            $argumentRequest = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                self::FORMAT
            );
        } catch (NotNormalizableValueException | UnexpectedValueException | NotEncodableValueException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }
        $violationList = $this->validator->validate($argumentRequest);

        if ($violationList->count()) {
            throw new ValidationFailedException($argumentRequest, $violationList);
        }

        yield $argumentRequest;
    }
}
