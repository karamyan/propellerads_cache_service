<?php

declare(strict_types=1);

namespace App\Controller;


use App\Modules\StatByDepartmentModule;
use App\Rules\StatByDepartmentRules;
use Exception;
use RedisException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;


class ApiStatsController extends AbstractController
{
    /**
     * @param Request $request
     * @param CacheInterface $redis
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws ValidationFailedException
     * @throws Exception
     */
    #[Route('/api/stats', name: 'statistics', methods: ['GET'])]
    public function getStatsByDepartmentAndDate(
        Request            $request,
        CacheInterface     $redis,
        ValidatorInterface $validator,
    ): JsonResponse
    {
        $body = $request->query->all();

        // Validate Data and return validation exception if is not valid.
        // TODO change validation part to before controller request, this is not the best practices in the symfony.
        $this->validate(body: $body, rules: StatByDepartmentRules::getStatRules(), validator: $validator);

        $response = StatByDepartmentModule::getData(body: $body, redis: $redis);

        return new JsonResponse(['data' => $response], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws ValidationFailedException
     * @throws RedisException
     */
    #[Route('/api/stats/calculate', name: 'calculate', methods: ['POST'])]
    public function calculate(
        Request            $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        // Validate Data and return validation exception if is not valid.
        // TODO change validation part to before controller request, this is not the best practices in the symfony.
        $this->validate(body: $body, rules: StatByDepartmentRules::getStatCalculateRules(), validator: $validator);

        StatByDepartmentModule::calculate($body);

        return new JsonResponse(['message' => 'Cache is updated successfully'], Response::HTTP_OK);
    }

    /**
     * @param array $body
     * @param Collection $rules
     * @param ValidatorInterface $validator
     * @return void
     */
    private function validate(array $body, Collection $rules, ValidatorInterface $validator): void
    {
        $violations = $validator->validate(value: $body, constraints: $rules);

        if ($violations->count()) {
            throw  new ValidationFailedException('uiu',$violations);
        }
    }
}
