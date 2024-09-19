<?php

namespace APIFeaturedImage\Admin;

interface PageInterface
{
    public static function process( ?array $_post ): ?bool;
    public static function render(): void;
}
