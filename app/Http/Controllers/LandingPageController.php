<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    protected $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index()
    {
        $stats = $this->sheetService->getStats();
        $programs = $this->sheetService->getPrograms();
        $documentation = $this->sheetService->getDocumentation();
        $blogPosts = $this->sheetService->getBlogPosts();
        $testimonials = $this->sheetService->getTestimonials();
        $contacts = $this->sheetService->getContacts();
        
        // programDetails has a nested structure and will be handled separately.
        $programDetails = [];

        return view('landing.layout', compact(
            'stats',
            'programs',
            'programDetails',
            'documentation',
            'blogPosts',
            'testimonials',
            'contacts'
        ));
    }
}
