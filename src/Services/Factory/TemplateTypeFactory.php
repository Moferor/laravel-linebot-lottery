<?php

namespace Jose13\LaravelLineBotLottery\Services\Factory;

use Exception;


class TemplateTypeFactory extends FactoryAbstract
{
    /**
     * 初始化該postback子服務 class的 所需要的模板
     * @throws Exception
     */
    public function makeTypeClass($templateType)
    {

        $templateTypeClass = config("LineBotServiceConfig.ClassRoute.Template.$templateType", false);

        if (!class_exists($templateTypeClass)) {

            throw new Exception('The Template Not Exits(Class Not Exits)');
        }
        return new $templateTypeClass;

    }
}
