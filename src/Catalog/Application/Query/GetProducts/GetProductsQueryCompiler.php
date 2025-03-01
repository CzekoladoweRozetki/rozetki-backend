<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\GetProducts;

use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;

class GetProductsQueryCompiler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Compiles a query to fetch products with search and filters.
     *
     * @param array<string, mixed> $filters
     */
    public function compile(
        ?string $search = null,
        array $filters = [],
        int $page = 1,
        int $limit = 10,
        ?string $categorySlug = null,
    ): Statement {
        $baseQuery = 'SELECT * FROM catalog_product';
        $where = [];
        $params = [];

        // search with fulltext search (tsvector) and fuzzy matching
        if ($search) {
            $where[] = 'search_vector @@ to_tsquery(:search)
                        OR similarity(name, :rawQuery) > 0.1
                        OR similarity(description, :rawQuery) > 0.1';
            $params['search'] = $this->buildPartialTsQuery($search);
            $params['rawQuery'] = $search;
        }

        // filter by category slug
        if ($categorySlug) {
            // Check if any category in the data->categories array has the specified slug
            $where[] = "EXISTS (
                        SELECT 1 FROM jsonb_array_elements(data->'categories') AS category
                        WHERE category->>'slug' = :categorySlug
                    )";
            $params['categorySlug'] = $categorySlug;
        }

        // order
        $orderBy = 'ORDER BY name ASC';

        // pagination
        $limitClause = 'LIMIT :limit OFFSET :offset';
        $params['limit'] = $limit;
        $params['offset'] = $limit * ($page - 1);

        $sql = $baseQuery;
        if ($where) {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' '.$orderBy.' '.$limitClause;

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt;
    }

    /**
     * Generates a partial TS query for partial-word matching.
     * e.g. "bike" => "bike:*"
     *      "electric bike" => "electric:* & bike:*".
     */
    private function buildPartialTsQuery(string $phrase): string
    {
        $parts = preg_split('/\s+/', trim($phrase));
        $parts = array_map(fn ($p) => $p.':*', $parts);

        return implode(' & ', $parts);
    }
}
