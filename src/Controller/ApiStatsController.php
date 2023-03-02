<?php

declare(strict_types=1);

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class ApiStatsController extends AbstractController
{
    /**
     * @param Request $request
     * @param CacheInterface $cache
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    #[Assert\NotNull(message: "datamarts is required")]
    #[Route('/api/stats', name: 'stat_list', methods: ['GET'])]
    public function getStatsByDepartmentAndDate(
        Request            $request,
        CacheInterface     $cache,
        ValidatorInterface $validator,


        string $datamarts = null
    ): JsonResponse
    {
        $datamarts = $request->get('datamarts');
        $dateFrom = $request->get('date_time_from');
        $dateTo = $request->get('date_time_to');
        $time = time();

        // Validate the input data
        $errors = $validator->validate([
            'datamarts' => $datamarts,
            'date_time_from' => $dateFrom,
            'date_time_to' => $dateTo,
        ]);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

$dateFrom = new DateTime($dateFrom);
$dateTo = new DateTime($dateTo);
$epochDateFrom = $dateFrom->getTimestamp();
$epochDateTo = $dateTo->getTimestamp();

sort($datamarts);
// create cache item for the key
$key = sprintf('%s|%s_%s', implode('_', $datamarts), $epochDateFrom, $epochDateTo);

$item = $cache->getItem($key);

// set the expiration time for the cache item (in seconds)
//        $item->expiresAfter(3600); // cache for 1 hour

// check if cache item is already in the cache
if (!$item->isHit()) {
    // Fetch data from data  access service.
    $data = [
        'result' => [],
        'time' => time()
    ];
    $item->set($data);
    $cache->save($item);

    $response = [
        'source' => 'DWH',
        "timestamp" => time()
    ];
} else {
    // Retrieve data from cache
    $data = $item->get();

    $response = [
        'source' => 'CACHE',
        "timestamp" => $data['time']
    ];
}

return new JsonResponse(['data' => $response], Response::HTTP_OK);
}

#[
Route('/api/stats/calculate', name: 'calculate', methods: ['POST'])]
    public function calculate(Request $request)
{
    dd($request);
    $datamarts = $request->get('datamarts');
    $datetime = $request->get('date_time');

    dd($datamarts, $datetime);
}
}
