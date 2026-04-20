<?php

/**
 * @since       10.09.2024 - 19:52
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Helper;

use Esit\Ctoadapter\Classes\Services\Adapter\Controller;
use Esit\Datacollections\Classes\Enums\DcaConfig;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class DcaHelper
{


    /**
     * Kann wegen Ringbezug nicht injected werden!
     *
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;


    /**
     * @param Controller $controller
     */
    public function __construct(private readonly Controller $controller)
    {
    }


    /**
     * @param CollectionFactory $collectionFactory
     *
     * @return void
     */
    public function setCollectionFactory(CollectionFactory $collectionFactory): void
    {
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * Gibt eine ArrayMap mit den Informationen für das LayzLoading zurück.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return ArrayCollection|null
     */
    public function getDepandancies(TablenameValue $tablename, FieldnameValue $fieldname): ?ArrayCollection
    {
        /* @phpstan-ignore-next-line */
        $this->controller->loadDataContainer($tablename->value());

        /** @var array<string, array<string, mixed>> $tlDca */
        $tlDca = $GLOBALS[DcaConfig::TL_DCA->name] ?? [];

        /** @var array<string, mixed> $tableConfig */
        $tableConfig = $tlDca[$tablename->value()] ?? [];

        /** @var array<string, mixed> $dcaField */
        $dcaField = $tableConfig[DcaConfig::fields->name] ?? [];

        if (!empty($dcaField)) {
            /** @var array<string, mixed> $fieldConfig */
            $fieldConfig = \is_array($dcaField[$fieldname->value()] ?? null) ? $dcaField[$fieldname->value()] : [];
            $lazyLoading = $fieldConfig[DcaConfig::lazyloading->name] ?? null;

            if (\is_array($lazyLoading)) {
                return $this->collectionFactory->createArrayCollection($lazyLoading);
            }
        }

        return null;
    }


    /**
     * Gibt den Namen des Felds mit der Eltern-Id in der Kindtabelle zurück.
     *
     * @param TablenameValue $tablename
     * @param TablenameValue $childtablename
     *
     * @return string
     */
    public function getChildDepandancies(TablenameValue $tablename, TablenameValue $childtablename): string
    {
        /* @phpstan-ignore-next-line */
        $this->controller->loadDataContainer($tablename->value());

        /** @var array<string, array<string, mixed>> $tlDca */
        $tlDca = $GLOBALS[DcaConfig::TL_DCA->name] ?? [];

        /** @var array<string, mixed> $tableConfig */
        $tableConfig = $tlDca[$tablename->value()] ?? [];

        /** @var array<string, mixed> $config */
        $config = \is_array($tableConfig[DcaConfig::config->name] ?? null) ? $tableConfig[DcaConfig::config->name] : [];

        if (!empty($config)) {
            /** @var array<string, mixed> $lazyloading */
            $lazyloading = \is_array($config[DcaConfig::lazyloading->name] ?? null) ? $config[DcaConfig::lazyloading->name] : [];
            $key         = $childtablename->value();
            $result      = $lazyloading[$key] ?? '';

            return \is_string($result) ? $result : '';
        }

        return '';
    }
}
