<?php

declare(strict_types=1);


namespace WooSimpleSeoAgent\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use WooSimpleSeoAgent\View\ViewRenderer;

class ViewRendererTest extends TestCase
{
    private ViewRenderer $renderer;
    private string $tempTemplate;

    protected function setUp(): void
    {
        $this->renderer = new ViewRenderer();

        $this->tempTemplate = sys_get_temp_dir() . '/test_template_' . uniqid() . '.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempTemplate)) {
            unlink($this->tempTemplate);
        }
    }

    public function test_render_includes_file_and_extracts_data(): void
    {
        file_put_contents($this->tempTemplate, '<?php echo "Hello " . $name; ?>');

        ob_start();
        $this->renderer->render($this->tempTemplate, ['name' => 'Łukasz']);
        $output = ob_get_clean();

        $this->assertEquals('Hello Łukasz', $output);
    }

    public function test_render_handles_missing_file(): void
    {
        $nonExistentPath = '/path/to/nothing.php';

        ob_start();
        $this->renderer->render($nonExistentPath);
        $output = ob_get_clean();

        $this->assertStringContainsString('Template missing.', $output);
    }

    public function test_render_isolates_scope(): void
    {
        file_put_contents($this->tempTemplate, '<?php echo isset($templatePath) ? "yes" : "no"; ?>');

        ob_start();
        $this->renderer->render($this->tempTemplate, ['name' => 'Test']);
        $output = ob_get_clean();

        $this->assertEquals('no', $output);
    }
}