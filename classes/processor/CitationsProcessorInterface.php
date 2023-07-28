<?php

namespace APP\plugins\generic\citations\classes\processor;

interface CitationsProcessorInterface
{
    public function process(string $doi, array $settings): array;
}