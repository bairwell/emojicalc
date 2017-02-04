<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

/**
 * Class Symbol
 *
 * Holds details of a single Unicode (or similar) symbol.
 *
 * @package Bairwell\Emojicalc
 */
class Symbol
{
    /**
     * Name of the symbol (such as "alien")
     * @var string
     */
    private $symbolName;

    /**
     * The unicode code for the symbol (or similar). i.e. "\u{1f47d}"
     * @var string
     */
    private $symbolCode;

    /**
     * Additional URL for the graphic for the symbol.
     * @var string
     */
    private $symbolImage;

    /**
     * Symbol constructor.
     *
     * @param string $symbolCode The unicode code for the symbol (or similar). i.e. "\u{1f47d}".
     * @param string $symbolName Name of the symbol (such as "alien").
     * @param string $symbolImage Additional URL for the graphic for the symbol.
     */
    public function __construct(string $symbolCode, string $symbolName = '', string $symbolImage = '')
    {
        $this->symbolCode = $symbolCode;
        $this->symbolName = $symbolName;
        $this->symbolImage = $symbolImage;
    }

    /**
     * Get this symbol code.
     * @return string
     */
    public function getSymbolCode(): string
    {
        return $this->symbolCode;
    }

    /**
     * Gets the name of the symbol
     * @return string
     */
    public function getSymbolName(): string
    {
        return $this->symbolName;
    }
}