<?php

namespace App\ApiResource\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Base\Constant\CommentWorkflow;
use App\Entity\Comment;
use Doctrine\ORM\QueryBuilder;

class FilterPublishedCommentQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if (Comment::class === $resourceClass) {
            $queryBuilder->andWhere(
                sprintf("%s.state = '%s'", $queryBuilder->getRootAliases()[0], CommentWorkflow::STATUS_PUBLISHED)
            );
        }
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        if (Comment::class === $resourceClass) {
            $queryBuilder->andWhere(
                sprintf("%s.state = '%s'", $queryBuilder->getRootAliases()[0], CommentWorkflow::STATUS_PUBLISHED)
            );
        }
    }
}
