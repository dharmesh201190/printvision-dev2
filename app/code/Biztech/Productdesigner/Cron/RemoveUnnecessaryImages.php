<?php

namespace Biztech\Productdesigner\Cron;

class RemoveUnnecessaryImages {

    protected $_infoHelper;
    protected $_filesystem;

    public function __construct(
    \Biztech\Productdesigner\Helper\Info $infoHelper, \Magento\Framework\Filesystem $filesystem) {
        $this->_infoHelper = $infoHelper;
        $this->_filesystem = $filesystem;
    }

    public function execute() {
        try {
            $reader = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $previewDirectory = $reader->getAbsolutePath() . '/productdesigner/preview';
            foreach (glob("{$previewDirectory}/*") as $file) {
                unlink($file);
            }
            $downloadDirectory = $reader->getAbsolutePath() . '/productdesigner/download';
            foreach (glob("{$downloadDirectory}/*") as $file) {
                unlink($file);
            }
        } catch (\Exception $e) {
            $this->_infoHelper->throwException($e, self::class);
        }
    }

}
