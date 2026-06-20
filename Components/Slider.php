<?php

class HomePageSlider
{
    private string $imageDir;
    private array $images = [];
    private static bool $stylesRendered = false;
    private static bool $scriptRendered = false;

    public function __construct(string $imageDir = null)
    {
        if ($imageDir === null) {
            $default = realpath(__DIR__ . '/../images');
            if ($default && is_dir($default)) {
                $imageDir = $default;
            } else {
                $imageDir = realpath(__DIR__ . '/images') ?: __DIR__;
            }
        }

        $this->imageDir = $imageDir;
        $this->images = $this->scanImages();
    }

    private function scanImages(): array
    {
        $images = [];
        if (!is_dir($this->imageDir)) {
            return [];
        }

        $files = scandir($this->imageDir);
        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $images[] = $file;
            }
        }

        sort($images, SORT_NATURAL | SORT_FLAG_CASE);
        return $images;
    }

    public function render(): void
    {
        if (empty($this->images)) {
            return;
        }

        if (!self::$stylesRendered) {
            $this->renderStyles();
            self::$stylesRendered = true;
        }

        $baseUrl = $this->getBaseUrl();
        $slides = [];

        foreach ($this->images as $index => $file) {
            $caption = $this->buildCaption($file);
            $active = $index === 0 ? ' active' : '';
            $slides[] = sprintf(
                '<div class="slide%s">' .
                '<img src="%s/%s" alt="%s">' .
                '<div class="slide-text">' .
                '<span class="slide-label">Secured Healthcare</span>' .
                '<h2>%s</h2>' .
                '<p>Access patient, doctor, lab, and admin services from one protected E-Health system.</p>' .
                '</div>' .
                '</div>',
                $active,
                htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($file, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($caption, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($caption, ENT_QUOTES, 'UTF-8')
            );
        }

        $buttons = [];
        foreach ($this->images as $index => $file) {
            $buttons[] = sprintf(
                '<button class="slider-dot%s" data-slide="%d" type="button" aria-label="Slide %d"></button>',
                $index === 0 ? ' active' : '',
                $index,
                $index + 1
            );
        }

        echo '<section class="ehsystem-slider">';
        echo '<div class="slider-container">';
        echo '<div class="slides">' . implode("\n", $slides) . '</div>';
        if (count($buttons) > 1) {
            echo '<div class="slider-controls">' . implode("\n", $buttons) . '</div>';
        }
        echo '</div>';
        echo '</section>';
        if (!self::$scriptRendered) {
            $this->renderScript();
            self::$scriptRendered = true;
        }
    }

    private function buildCaption(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        $name = trim($name);
        return $name !== '' ? ucwords($name) : 'Home Slider';
    }

    private function getBaseUrl(): string
    {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        $imageDirReal = realpath($this->imageDir);

        if ($docRoot && $imageDirReal && strpos($imageDirReal, $docRoot) === 0) {
            $path = str_replace('\\', '/', substr($imageDirReal, strlen($docRoot)));
            return $path !== '' ? $path : '/images';
        }

        return '/images';
    }

    private function renderStyles(): void
    {
        echo '<style>' .
            '.ehsystem-slider{position:relative;overflow:hidden;background:#12202f;color:#fff;margin:0 auto 35px;border-radius:8px;}' .
            '.ehsystem-slider .slider-container{position:relative;width:100%;min-height:340px;aspect-ratio:16/6;}' .
            '.ehsystem-slider .slides{position:absolute;inset:0;overflow:hidden;}' .
            '.ehsystem-slider .slide{position:absolute;inset:0;opacity:0;visibility:hidden;transition:opacity .7s ease,visibility .7s ease;}' .
            '.ehsystem-slider .slide.active{opacity:1;visibility:visible;}' .
            '.ehsystem-slider img{width:100%;height:100%;display:block;object-fit:cover;}' .
            '.ehsystem-slider .slide:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(18,32,47,.78),rgba(18,32,47,.28),rgba(18,32,47,.1));}' .
            '.ehsystem-slider .slide-text{position:absolute;z-index:1;left:36px;bottom:34px;right:36px;max-width:650px;}' .
            '.ehsystem-slider .slide-label{display:inline-block;margin-bottom:10px;padding:6px 12px;background:#99cc00;color:#17220a;border-radius:4px;font-size:12px;font-weight:bold;text-transform:uppercase;}' .
            '.ehsystem-slider h2{margin:0 0 10px;font-size:34px;line-height:1.15;color:#fff;}' .
            '.ehsystem-slider p{margin:0;font-size:16px;line-height:1.5;color:#eef5f8;}' .
            '.ehsystem-slider .slider-controls{position:absolute;z-index:2;right:28px;bottom:24px;display:flex;gap:9px;}' .
            '.ehsystem-slider .slider-dot{width:12px;height:12px;border-radius:50%;border:2px solid rgba(255,255,255,.86);background:transparent;cursor:pointer;padding:0;transition:background .2s ease,border-color .2s ease;}' .
            '.ehsystem-slider .slider-dot.active,.ehsystem-slider .slider-dot:hover{background:#99cc00;border-color:#99cc00;}' .
            '@media(max-width:768px){.ehsystem-slider{border-radius:6px;margin-bottom:25px;}.ehsystem-slider .slider-container{min-height:260px;aspect-ratio:auto;}.ehsystem-slider .slide-text{left:20px;right:20px;bottom:28px;}.ehsystem-slider h2{font-size:25px;}.ehsystem-slider p{font-size:14px;}.ehsystem-slider .slider-controls{right:20px;bottom:16px;}}' .
            '</style>';
    }

    private function renderScript(): void
    {
        echo '<script>' .
            '(function(){' .
            'document.querySelectorAll(".ehsystem-slider").forEach(function(root){' .
            'const dots=root.querySelectorAll(".slider-dot");' .
            'const slides=root.querySelectorAll(".slide");' .
            'let index=0;const total=slides.length;let interval=null;' .
            'function goTo(i){if(!total){return;}slides.forEach(function(slide,idx){slide.classList.toggle("active",idx===i);});dots.forEach(function(dot,idx){dot.classList.toggle("active",idx===i);});index=i;}' .
            'function next(){goTo((index+1)%total);}' .
            'function resetInterval(){if(total<2){return;}clearInterval(interval);interval=setInterval(next,6000);}' .
            'dots.forEach(function(dot){dot.addEventListener("click",function(){goTo(Number(this.dataset.slide));resetInterval();});});' .
            'resetInterval();' .
            '});' .
            '})();' .
            '</script>';
    }
}

function renderHomePageSlider(string $imageDir = null): void
{
    $slider = new HomePageSlider($imageDir);
    $slider->render();
}
