<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2014 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/gpl-license GPL-3.0
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.net/scssphp
 */

namespace Biztech\ThemeColors\Scss;
use  \Magento\Framework\Controller\ResultFactory;

/**
 * SCSS base formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
abstract class Formatter
{
    protected $resultFactory;
    public $indentLevel;
    public $indentChar;
    public $break;
    public $open;
    public $close;
    public $tagSeparator;
    public $assignSeparator;

    public function __construct(
        ResultFactory $resultFactory
    ) {
       $this->resultFactory = $resultFactory;
    } 

    protected function indentStr($n = 0)
    {
        return str_repeat($this->indentChar, max($this->indentLevel + $n, 0));
    }

    public function property($name, $value)
    {
        return $name . $this->assignSeparator . $value . ';';
    }

    protected function blockLines($inner, $block)
    {
        $glue = $this->break.$inner;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultJson->setContents($inner . implode($glue, $block->lines) );
        // echo $inner . implode($glue, $block->lines);

        if (!empty($block->children)) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultJson->setContents($this->break);
           // echo $this->break;
        }
    }

    protected function block($block)
    {
        if (empty($block->lines) && empty($block->children)) {
            return;
        }

        $inner = $pre = $this->indentStr();

        if (!empty($block->selectors)) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultJson->setContents($pre .
                implode($this->tagSeparator, $block->selectors) .
                $this->open . $this->break);
           /* echo $pre .
                implode($this->tagSeparator, $block->selectors) .
                $this->open . $this->break;*/
            $this->indentLevel++;
            $inner = $this->indentStr();
        }

        if (!empty($block->lines)) {
            $this->blockLines($inner, $block);
        }

        foreach ($block->children as $child) {
            $this->block($child);
        }

        if (!empty($block->selectors)) {
            $this->indentLevel--;

            if (empty($block->children)) {
                 $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
                $resultJson->setContents($this->break);
                //echo $this->break;
            }
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultJson->setContents($pre . $this->close . $this->break);
            //echo $pre . $this->close . $this->break;
        }
    }

    public function format($block)
    {
        ob_start();
        $this->block($block);
        $out = ob_get_clean();

        return $out;
    }
}
