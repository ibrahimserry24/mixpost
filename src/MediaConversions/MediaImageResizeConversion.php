<?php

namespace Inovector\Mixpost\MediaConversions;

use Inovector\Mixpost\Abstracts\MediaConversion;
use Inovector\Mixpost\Support\MediaConversionData;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaImageResizeConversion extends MediaConversion
{
    protected float|null $width;
    protected float|null $height = null;

    public function getEngineName(): string
    {
        return 'ImageResize';
    }

    public function canPerform(): bool
    {
        return $this->isImage() && !$this->isGifImage();
    }

    public function getPath(): string
    {
        return $this->getFilePathWithSuffix();
    }

    public function width(float|null $value = null): static
    {
        $this->width = $value;

        return $this;
    }

    public function height(float|null $value = null): static
    {
        $this->height = $value;

        return $this;
    }

    public function handle(): MediaConversionData|null
    {
        // TODO: Check if works with S3 driver
        $content = $this->filesystem($this->getFromDisk())->get($this->getFilepath());

        $manager = new ImageManager(new Driver());
        $image = $manager->read($content);

        $convert = $image->scale($this->width, $this->height)->encode();

        $this->filesystem()->put($this->getPath(), $convert->getEncoded(), 'public');

        return MediaConversionData::conversion($this);
    }
}
