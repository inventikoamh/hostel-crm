<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverter;

class DocumentationController extends Controller
{
    /**
     * Display the documentation index page
     */
    public function index()
    {
        $docsPath = base_path('docs');
        $files = $this->getMarkdownFiles($docsPath);
        
        return view('docs.index', [
            'files' => $files,
            'currentFile' => null,
            'content' => $this->getIndexContent()
        ]);
    }

    /**
     * Display a specific documentation file
     */
    public function show(Request $request, $file = null)
    {
        $docsPath = base_path('docs');
        
        // If no file specified, show index
        if (!$file) {
            return $this->index();
        }

        // Security: Only allow .md files and prevent directory traversal
        $file = str_replace(['../', '..\\'], '', $file);
        $filePath = $docsPath . DIRECTORY_SEPARATOR . $file . '.md';
        
        // Debug logging
        \Log::info('Documentation file request', [
            'file' => $file,
            'filePath' => $filePath,
            'exists' => File::exists($filePath)
        ]);
        
        // Check if file exists
        if (!File::exists($filePath)) {
            abort(404, 'Documentation file not found: ' . $filePath);
        }

        // Get all available files for navigation
        $files = $this->getMarkdownFiles($docsPath);
        
        // Read and parse markdown content
        $content = $this->parseMarkdown(File::get($filePath));
        
        return view('docs.show', [
            'files' => $files,
            'currentFile' => $file,
            'content' => $content,
            'title' => $this->getFileTitle($file)
        ]);
    }

    /**
     * Get all markdown files from docs directory
     */
    private function getMarkdownFiles($docsPath)
    {
        $files = [];
        
        if (File::exists($docsPath)) {
            $allFiles = File::allFiles($docsPath);
            
            foreach ($allFiles as $file) {
                if ($file->getExtension() === 'md') {
                    $relativePath = str_replace($docsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                    $fileName = str_replace('.md', '', $relativePath);
                    
                    $files[] = [
                        'name' => $fileName,
                        'path' => $fileName,
                        'title' => $this->getFileTitle($fileName),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime()
                    ];
                }
            }
        }
        
        // Sort files alphabetically
        usort($files, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        return $files;
    }

    /**
     * Get file title from filename or content
     */
    private function getFileTitle($fileName)
    {
        // Convert filename to title
        $title = str_replace(['-', '_'], ' ', $fileName);
        $title = ucwords($title);
        
        // Special cases
        $specialTitles = [
            'api-documentation' => 'API Documentation',
            'api/authentication' => 'Authentication API',
            'api/hostels' => 'Hostels API',
            'api/tenants' => 'Tenants API',
            'api/invoices' => 'Invoices API',
            'api/payments' => 'Payments API',
            'api/amenities' => 'Amenities API',
            'api/users' => 'Users API',
            'api/enquiries' => 'Enquiries API',
            'api/notifications' => 'Notifications API',
            'api/dashboard' => 'Dashboard API',
            'modules/authentication' => 'Authentication Module',
            'modules/hostel' => 'Hostel Module',
            'modules/tenant' => 'Tenant Module',
            'modules/invoice' => 'Invoice Module',
            'modules/payment' => 'Payment Module',
            'modules/amenity-usage' => 'Amenity Usage Module',
            'modules/dashboard' => 'Dashboard Module',
            'modules/enquiry' => 'Enquiry Module',
            'modules/notification' => 'Notification Module',
            'modules/user-management' => 'User Management Module',
            'modules/paid-amenities' => 'Paid Amenities Module',
            'modules/tenant-profile-update-requests' => 'Tenant Profile Update Requests',
            'modules/usage-correction-requests' => 'Usage Correction Requests',
            'modules/components' => 'Components Module',
            'modules/availability' => 'Availability Module',
            'modules/map' => 'Map Module',
            'modules/room' => 'Room Module',
            'billing-cycle-system' => 'Billing Cycle System',
            'component-standards' => 'Component Standards',
            'deployment-setup' => 'Deployment Setup',
            'landing-page-specification' => 'Landing Page Specification',
            'layout-standards' => 'Layout Standards',
            'table-standards' => 'Table Standards',
            'CHANGELOG' => 'Changelog',
            'README' => 'README'
        ];
        
        return $specialTitles[$fileName] ?? $title;
    }

    /**
     * Parse markdown content to HTML using CommonMark
     */
    private function parseMarkdown($content)
    {
        // Configure CommonMark environment
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 10,
        ]);
        
        // Add extensions
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new AutolinkExtension());
        
        // Create converter
        $converter = new MarkdownConverter($environment);
        
        // Convert markdown to HTML
        $html = $converter->convert($content)->getContent();
        
        // Post-process HTML to add Tailwind classes
        $html = $this->addTailwindClasses($html);
        
        return $html;
    }

    /**
     * Add Tailwind CSS classes to HTML elements
     */
    private function addTailwindClasses($html)
    {
        // Headers
        $html = preg_replace('/<h1>/', '<h1 class="text-3xl font-bold text-gray-900 mb-6 mt-8">', $html);
        $html = preg_replace('/<h2>/', '<h2 class="text-2xl font-semibold text-gray-800 mb-4 mt-8">', $html);
        $html = preg_replace('/<h3>/', '<h3 class="text-xl font-semibold text-gray-700 mb-3 mt-6">', $html);
        $html = preg_replace('/<h4>/', '<h4 class="text-lg font-semibold text-gray-700 mb-3 mt-6">', $html);
        $html = preg_replace('/<h5>/', '<h5 class="text-base font-semibold text-gray-700 mb-2 mt-4">', $html);
        $html = preg_replace('/<h6>/', '<h6 class="text-sm font-semibold text-gray-700 mb-2 mt-4">', $html);
        
        // Paragraphs
        $html = preg_replace('/<p>/', '<p class="text-gray-600 mb-4 leading-relaxed">', $html);
        
        // Lists
        $html = preg_replace('/<ul>/', '<ul class="list-disc list-inside mb-4 space-y-2">', $html);
        $html = preg_replace('/<ol>/', '<ol class="list-decimal list-inside mb-4 space-y-2">', $html);
        $html = preg_replace('/<li>/', '<li class="text-gray-600">', $html);
        
        // Enhanced code block handling
        $html = $this->processCodeBlocks($html);
        
        // Tables
        $html = preg_replace('/<table>/', '<table class="w-full border-collapse border border-gray-300 mb-4">', $html);
        $html = preg_replace('/<th>/', '<th class="border border-gray-300 px-4 py-2 bg-gray-100 font-semibold text-left">', $html);
        $html = preg_replace('/<td>/', '<td class="border border-gray-300 px-4 py-2">', $html);
        
        // Links
        $html = preg_replace('/<a href="/', '<a href="', $html);
        $html = preg_replace('/<a href="([^"]*)">/', '<a href="$1" class="text-blue-600 hover:text-blue-800 underline">', $html);
        
        // Blockquotes
        $html = preg_replace('/<blockquote>/', '<blockquote class="border-l-4 border-blue-500 pl-4 italic text-gray-600 my-4">', $html);
        
        // Strong and emphasis
        $html = preg_replace('/<strong>/', '<strong class="font-semibold text-gray-900">', $html);
        $html = preg_replace('/<em>/', '<em class="italic text-gray-700">', $html);
        
        // Horizontal rules
        $html = preg_replace('/<hr>/', '<hr class="border-gray-300 my-8">', $html);
        
        return $html;
    }

    /**
     * Process code blocks with enhanced language support
     */
    private function processCodeBlocks($html)
    {
        // Language mapping for better syntax highlighting
        $languageMap = [
            'php' => 'php',
            'javascript' => 'javascript',
            'js' => 'javascript',
            'json' => 'json',
            'bash' => 'bash',
            'shell' => 'bash',
            'sh' => 'bash',
            'css' => 'css',
            'html' => 'markup',
            'xml' => 'markup',
            'sql' => 'sql',
            'python' => 'python',
            'py' => 'python',
            'java' => 'java',
            'csharp' => 'csharp',
            'cs' => 'csharp',
            'cpp' => 'cpp',
            'c++' => 'cpp',
            'c' => 'c',
            'go' => 'go',
            'rust' => 'rust',
            'ruby' => 'ruby',
            'rb' => 'ruby',
            'swift' => 'swift',
            'kotlin' => 'kotlin',
            'dart' => 'dart',
            'typescript' => 'typescript',
            'ts' => 'typescript',
            'yaml' => 'yaml',
            'yml' => 'yaml',
            'dockerfile' => 'dockerfile',
            'docker' => 'dockerfile',
            'markdown' => 'markdown',
            'md' => 'markdown',
            'text' => 'text',
            'plain' => 'text',
            'diff' => 'diff',
            'git' => 'git',
            'http' => 'http',
            'curl' => 'bash',
            'terminal' => 'bash',
            'console' => 'bash',
            'cmd' => 'bash',
            'powershell' => 'powershell',
            'ps1' => 'powershell',
            'vim' => 'vim',
            'viml' => 'vim',
            'nginx' => 'nginx',
            'apache' => 'apache',
            'conf' => 'apache',
            'ini' => 'ini',
            'toml' => 'toml',
            'xml' => 'markup',
            'svg' => 'markup',
            'r' => 'r',
            'scala' => 'scala',
            'haskell' => 'haskell',
            'hs' => 'haskell',
            'clojure' => 'clojure',
            'clj' => 'clojure',
            'elixir' => 'elixir',
            'ex' => 'elixir',
            'elm' => 'elm',
            'lua' => 'lua',
            'perl' => 'perl',
            'pl' => 'perl',
            'r' => 'r',
            'matlab' => 'matlab',
            'octave' => 'matlab',
            'fortran' => 'fortran',
            'f90' => 'fortran',
            'cobol' => 'cobol',
            'pascal' => 'pascal',
            'ada' => 'ada',
            'prolog' => 'prolog',
            'erlang' => 'erlang',
            'erl' => 'erlang',
            'ocaml' => 'ocaml',
            'ml' => 'ocaml',
            'fsharp' => 'fsharp',
            'fs' => 'fsharp',
            'vb' => 'vbnet',
            'vbnet' => 'vbnet',
            'assembly' => 'asm',
            'asm' => 'asm',
            'x86' => 'asm',
            'arm' => 'asm',
            'mips' => 'asm',
            'risc' => 'asm',
            '6502' => 'asm',
            'z80' => 'asm',
            '6502' => 'asm',
            'basic' => 'basic',
            'vb' => 'basic',
            'qbasic' => 'basic',
            'gwbasic' => 'basic',
            'cobol' => 'cobol',
            'fortran' => 'fortran',
            'algol' => 'algol',
            'pl1' => 'pl1',
            'apl' => 'apl',
            'j' => 'j',
            'k' => 'k',
            'q' => 'q',
            'mathematica' => 'mathematica',
            'mma' => 'mathematica',
            'wolfram' => 'mathematica',
            'maxima' => 'maxima',
            'sage' => 'sage',
            'gap' => 'gap',
            'magma' => 'magma',
            'maple' => 'maple',
            'matlab' => 'matlab',
            'octave' => 'octave',
            'scilab' => 'scilab',
            'gnuplot' => 'gnuplot',
            'graphviz' => 'graphviz',
            'dot' => 'graphviz',
            'gv' => 'graphviz',
            'plantuml' => 'plantuml',
            'puml' => 'plantuml',
            'uml' => 'plantuml',
            'mermaid' => 'mermaid',
            'mmd' => 'mermaid',
            'flowchart' => 'mermaid',
            'sequence' => 'mermaid',
            'gantt' => 'mermaid',
            'class' => 'mermaid',
            'state' => 'mermaid',
            'journey' => 'mermaid',
            'gitgraph' => 'mermaid',
            'pie' => 'mermaid',
            'requirement' => 'mermaid',
            'c4' => 'mermaid',
            'mindmap' => 'mermaid',
            'timeline' => 'mermaid',
            'sankey' => 'mermaid',
            'block' => 'mermaid',
            'block-beta' => 'mermaid',
            'block-delta' => 'mermaid',
            'block-gamma' => 'mermaid',
            'block-pi' => 'mermaid',
            'block-tau' => 'mermaid',
            'block-omega' => 'mermaid',
            'block-alpha' => 'mermaid',
            'block-beta' => 'mermaid',
            'block-gamma' => 'mermaid',
            'block-delta' => 'mermaid',
            'block-epsilon' => 'mermaid',
            'block-zeta' => 'mermaid',
            'block-eta' => 'mermaid',
            'block-theta' => 'mermaid',
            'block-iota' => 'mermaid',
            'block-kappa' => 'mermaid',
            'block-lambda' => 'mermaid',
            'block-mu' => 'mermaid',
            'block-nu' => 'mermaid',
            'block-xi' => 'mermaid',
            'block-omicron' => 'mermaid',
            'block-pi' => 'mermaid',
            'block-rho' => 'mermaid',
            'block-sigma' => 'mermaid',
            'block-tau' => 'mermaid',
            'block-upsilon' => 'mermaid',
            'block-phi' => 'mermaid',
            'block-chi' => 'mermaid',
            'block-psi' => 'mermaid',
            'block-omega' => 'mermaid',
        ];

        // Process code blocks with language specification
        $html = preg_replace_callback('/<pre><code class="language-([^"]*)">(.*?)<\/code><\/pre>/s', function($matches) use ($languageMap) {
            $language = strtolower(trim($matches[1]));
            $code = htmlspecialchars_decode($matches[2]);
            
            // Map language to proper Prism.js language
            $prismLanguage = $languageMap[$language] ?? $language;
            
            // Special handling for certain languages
            if (in_array($language, ['http', 'curl', 'terminal', 'console', 'cmd'])) {
                $prismLanguage = 'bash';
            } elseif (in_array($language, ['html', 'xml', 'svg'])) {
                $prismLanguage = 'markup';
            } elseif (in_array($language, ['text', 'plain', ''])) {
                $prismLanguage = 'text';
            }
            
            return sprintf(
                '<pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-4 border border-gray-700"><code class="language-%s bg-transparent text-gray-100 p-0 block">%s</code></pre>',
                $prismLanguage,
                htmlspecialchars($code)
            );
        }, $html);

        // Process code blocks without language specification
        $html = preg_replace_callback('/<pre><code>(.*?)<\/code><\/pre>/s', function($matches) {
            $code = htmlspecialchars_decode($matches[1]);
            
            // Try to detect language from content
            $detectedLanguage = $this->detectLanguageFromContent($code);
            
            return sprintf(
                '<pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-4 border border-gray-700"><code class="language-%s bg-transparent text-gray-100 p-0 block">%s</code></pre>',
                $detectedLanguage,
                htmlspecialchars($code)
            );
        }, $html);

        // Process inline code (not inside pre blocks)
        $html = preg_replace('/<code>(?![^<]*<\/pre>)([^<]+)<\/code>/', '<code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-mono border">$1</code>', $html);

        return $html;
    }

    /**
     * Detect language from code content
     */
    private function detectLanguageFromContent($code)
    {
        $code = trim($code);
        
        // Common patterns for language detection
        if (preg_match('/^<\?php|<\?=|use [A-Z]|class [A-Z]|function [a-zA-Z]|->|::/', $code)) {
            return 'php';
        }
        
        if (preg_match('/^\{|\}$|"([^"]*)":|,(\s*)$/', $code)) {
            return 'json';
        }
        
        if (preg_match('/^GET |POST |PUT |DELETE |PATCH |HEAD |OPTIONS |HTTP\/|Content-Type:|Authorization:|curl -X/', $code)) {
            return 'bash';
        }
        
        if (preg_match('/^SELECT |INSERT |UPDATE |DELETE |CREATE |ALTER |DROP |FROM |WHERE |JOIN /i', $code)) {
            return 'sql';
        }
        
        if (preg_match('/^#!\/bin\/|^#!\/usr\/bin\/|^sudo |^apt |^yum |^npm |^composer |^git |^docker /', $code)) {
            return 'bash';
        }
        
        if (preg_match('/^<[^>]+>|<\/[^>]+>|<!DOCTYPE|xml version/', $code)) {
            return 'markup';
        }
        
        if (preg_match('/^function |^const |^let |^var |^=>|^import |^export |^console\./', $code)) {
            return 'javascript';
        }
        
        if (preg_match('/^Route::|^use Illuminate|^namespace App|^class [A-Z] extends/', $code)) {
            return 'php';
        }
        
        if (preg_match('/^curl -X|^GET |^POST |^PUT |^DELETE |^PATCH /', $code)) {
            return 'bash';
        }
        
        if (preg_match('/^# |^## |^### |^#### |^##### |^###### |^\- |^\* |^\d+\. /', $code)) {
            return 'markdown';
        }
        
        if (preg_match('/^\.|^#|^\/\*|\*\/|^\/\/|^\/\*|\*\/$/', $code)) {
            return 'text';
        }
        
        // Default to text if no pattern matches
        return 'text';
    }

    /**
     * Get index content
     */
    private function getIndexContent()
    {
        $content = "# Hostel CRM Documentation\n\n";
        $content .= "Welcome to the Hostel CRM system documentation. This documentation covers all aspects of the system including API endpoints, modules, and deployment instructions.\n\n";
        $content .= "## Available Documentation\n\n";
        $content .= "Use the navigation menu on the left to browse through different sections of the documentation.\n\n";
        $content .= "## Quick Links\n\n";
        $content .= "- [API Documentation](api-documentation) - Complete API reference\n";
        $content .= "- [Authentication API](api/authentication) - Authentication endpoints\n";
        $content .= "- [Deployment Setup](deployment-setup) - How to deploy the system\n";
        $content .= "- [Component Standards](component-standards) - UI component guidelines\n";
        $content .= "- [Changelog](CHANGELOG) - Recent changes and updates\n\n";
        $content .= "## Getting Started\n\n";
        $content .= "1. Review the [Deployment Setup](deployment-setup) guide\n";
        $content .= "2. Check the [API Documentation](api-documentation) for integration\n";
        $content .= "3. Explore individual modules for detailed information\n\n";
        $content .= "For questions or support, please refer to the relevant module documentation.";
        
        return $this->parseMarkdown($content);
    }
}
