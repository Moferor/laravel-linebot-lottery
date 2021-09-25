<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType;


use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;


abstract class TemplateTypeAbstract implements TemplateTypeInterface
{
    protected array $balls;
    protected string $black = '#000000';
    protected string $gray = '#6C6C6C';
    protected string $blue = '#0000E3';
    protected string $red = '#EA0000';
    /**
     * @var mixed
     */
    protected $heard;
    /**
     * @var mixed
     */
    protected $body;
    /**
     * @var mixed
     */
    protected $footer;


    abstract function getBubbleBuild($ballData):array;


    /** 橫線模板
     * @param $color
     * @param $margin
     * @return SeparatorComponentBuilder
     */
    public function createSeparatorStyle($color, $margin): SeparatorComponentBuilder
    {
        return SeparatorComponentBuilder::builder()
            ->setColor($color)
            ->setMargin($margin);
    }


    /**
     * 公用 Box裡的Text模板
     * @param $text
     * @param $size
     * @param $color
     * @param $weight
     * @param null $margin
     * @param null $flex
     * @param null $align
     * @return TextComponentBuilder
     */
    public function createTextStyle($text, $size, $color, $weight, $margin = null, $flex = null, $align = null): TextComponentBuilder
    {
        return TextComponentBuilder::builder()
            ->setText($text)
            ->setSize($size)
            ->setColor($color)
            ->setWeight($weight)
            ->setMargin($margin)
            ->setFlex($flex)
            ->setAlign($align);
    }


}
