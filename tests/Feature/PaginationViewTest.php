<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    public function test_global_pagination_has_responsive_indonesian_navigation(): void
    {
        $paginator = new LengthAwarePaginator(
            items: range(11, 20),
            total: 30,
            perPage: 10,
            currentPage: 2,
            options: ['path' => '/products'],
        );

        $html = $paginator->links()->render();

        $this->assertStringContainsString('Navigasi halaman', $html);
        $this->assertStringContainsString('Menampilkan', $html);
        $this->assertStringContainsString('2 <span class="font-medium text-blue-200">/ 3</span>', $html);
        $this->assertStringContainsString('from-brand-blue to-blue-600', $html);
        $this->assertStringContainsString('Buka halaman 3', $html);
        $this->assertStringNotContainsString('dark:bg-gray-800', $html);
    }
}
