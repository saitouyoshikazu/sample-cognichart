<?php

namespace App\Domain\AbstractChartTerm\Strategy;
use App\Domain\Chart\ChartEntity;
use App\Domain\AbstractChartTerm\AbstractChartTermException;

class StrategyFactory
{

    public function createRequestSender(ChartEntity $chartEntity)
    {
        $strategyName = $chartEntity->strategyName();
        if (empty($strategyName)) {
            throw new AbstractChartTermException("RequestSender of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $requestSenderNamespace = 'App\Domain\AbstractChartTerm\Strategy\\' . $strategyName . '\RequestSender';
        if (!class_exists($requestSenderNamespace)) {
            throw new AbstractChartTermException("RequestSender of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $requestSender = app($requestSenderNamespace);
        $requestSender
            ->setScheme($chartEntity->scheme())
            ->setHost($chartEntity->host())
            ->setUri($chartEntity->uri());
        return $requestSender;
    }

    public function createDomAnalyzer(ChartEntity $chartEntity)
    {
        $strategyName = $chartEntity->strategyName();
        if (empty($strategyName)) {
            throw new AbstractChartTermException("DomAnalyzer of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $domAnalyzerNamespace = 'App\Domain\AbstractChartTerm\Strategy\\' . $strategyName . '\DomAnalyzer';
        if (!class_exists($domAnalyzerNamespace)) {
            throw new AbstractChartTermException("DomAnalyzer of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $domAnalyzer = app($domAnalyzerNamespace);
        return $domAnalyzer;
    }

    public function createAdoptionCriteria(ChartEntity $chartEntity)
    {
        $strategyName = $chartEntity->strategyName();
        if (empty($strategyName)) {
            throw new AbstractChartTermException("AdoptionCriteria of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $adoptionCriteriaNamespace = 'App\Domain\AbstractChartTerm\Strategy\\' . $strategyName . '\AdoptionCriteria';
        if (!class_exists($adoptionCriteriaNamespace)) {
            throw new AbstractChartTermException("AdoptionCriteria of {$chartEntity->businessId()->value()} doesn't exist.");
        }
        $adoptionCriteria = app($adoptionCriteriaNamespace);
        return $adoptionCriteria;
    }

}
