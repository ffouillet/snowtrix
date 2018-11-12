<?php
namespace Tests\CoreBundle\Service;

use PHPUnit\Framework\TestCase;
use CoreBundle\Service\FxStringsTools;

class FxStringsToolsTest extends TestCase {

    /**
     * @param string $string
     * @param string $slug
     * @dataProvider getSlugs
     */
    public function testQuickSlugify ($string, $expectedSlug) {

        $this->assertSame($expectedSlug,FxStringsTools::quickSlugify($string));
    }

    public function getSlugs()
    {
        return [
            ['A-Test-Slug','a-test-slug'],
            ['A-Téste-Slugue','a-teste-slugue'],
            ['--A--Test-SluG--','a-test-slug'],
            ['j\'aime méttre des àccents partout, ça me plait', 'j-aime-mettre-des-accents-partout-ca-me-plait']
        ];
    }
}