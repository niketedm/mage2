<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-sorting
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Sorting\Factor;

use Mirasvit\Sorting\Api\Data\RankingFactorInterface;
use Mirasvit\Sorting\Model\Indexer\FactorIndexer;

class DateFactor implements FactorInterface
{
    use ScoreTrait;

    const DATE_FIELD = 'date_field';
    const ZERO_POINT = 'zero_point';

    private $context;

    private $indexer;

    public function __construct(
        Context $context,
        FactorIndexer $indexer
    ) {
        $this->context = $context;
        $this->indexer = $indexer;
    }

    public function getName(): string
    {
        return 'Date';
    }

    public function getDescription(): string
    {
        return 'Rank products based on Created At/Updated At date.';
    }

    public function getUiComponent(): ?string
    {
        return 'sorting_factor_date';
    }

    public function reindex(RankingFactorInterface $rankingFactor, array $productIds): void
    {
        $dateField = (string)$rankingFactor->getConfigData(self::DATE_FIELD, 'created_at');
        $zeroPoint = (int)$rankingFactor->getConfigData(self::ZERO_POINT, 60);

        $resource   = $this->indexer->getResource();
        $connection = $resource->getConnection();

        $select = $connection->select();
        $select->from(
            ['e' => $resource->getTableName('catalog_product_entity')],
            ['entity_id', $dateField]
        );

        if ($productIds) {
            $select->where('entity_id IN (?)', $productIds);
        }

        $stmt = $connection->query($select);

        $this->indexer->process($rankingFactor, $productIds, function () use ($stmt, $dateField, $zeroPoint) {
            while ($row = $stmt->fetch()) {
                $createdAt = $row[$dateField];
                $days      = $this->getDaysDiff($createdAt);

                $score = $zeroPoint > $days
                    ? $this->normalize($zeroPoint - $days, 0, $zeroPoint)
                    : 0;

                $this->indexer->add((int)$row['entity_id'], $score, $createdAt);
            }
        });
    }

    private function getDaysDiff(string $date): float
    {
        return (time() - strtotime($date)) / 60 / 60 / 24;
    }
}
