<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
class EnquiryController extends Controller
{

    public function editPrivacyPolicy(Request $request)
    {
        $type = $request->type ?? 'customer';

        $privacyPolicy = PrivacyPolicy::where('type', $type)->first();

        return view('admin.PrivacyPolicy.edit', compact('privacyPolicy', 'type'));
    }

    public function updatePrvacyPolicy(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
           'content' => 'required|string',
            'type' => 'required|in:vendor,customer',
        ], [
            'title.required' => 'Please enter the privacy policy title.',
            'content.required' => 'Please enter the privacy policy content.',
              'type.required' => 'Please enter the privacy policy type.',
        ]);
        // Clean HTML before saving
        $cleanedTitle = $this->cleanHtml($request->title);
        $cleanedContent = $this->cleanHtml($request->content);

        // check if record exists
        $policy = PrivacyPolicy::where('type', $request->type)->first();

        $data = [
            'title'   => $cleanedTitle,
            'content' => $cleanedContent,
            'type'    => $request->type,
        ];

        if ($policy) {
           $policy->update($data);
        } else {
            PrivacyPolicy::create($data);
        }

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }
    


    public function editTermsConditions(Request $request)
    {
        $type = $request->type ?? 'customer';

        $privacyPolicy = TermsCondition::where('type', $type)->first();

        return view('admin.PrivacyPolicy.editTermCondition', compact('privacyPolicy', 'type'));
    }

    public function updateTermsConditions(Request $request)
    {
        $request->validate([
            'title'   => 'required|string',
            'content' => 'required|string',
              'type'    => 'required|in:vendor,customer',
        ], [
            'title.required'   => 'Please enter the title.',
            'content.required' => 'Please enter the content.',
            'content.required' => 'Please enter the type.',
        ]);

        // Clean HTML before saving
        $cleanedTitle = $this->cleanHtml($request->title);
        $cleanedContent = $this->cleanHtml($request->content);

        $policy = TermsCondition::where('type', $request->type)->first();

        $data = [
            'title'   => $cleanedTitle,
            'content' => $cleanedContent,
            'type'    => $request->type,
        ];

        if ($policy) {
            $policy->update($data);
        } else {
            TermsCondition::create($data);
        }

        return redirect()->back()->with('success', 'Terms & Conditions updated successfully.');
    }

    /**
     * Clean CKEditor HTML
     */
    private function cleanHtml($html)
    {
        libxml_use_internal_errors(true); // Prevent HTML5 warnings

        $doc = new \DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        // Remove unwanted attributes
        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//*[@data-start or @data-end]') as $node) {
            $node->removeAttribute('data-start');
            $node->removeAttribute('data-end');
        }

        // Remove <p> inside <li>
        foreach ($xpath->query('//li/p') as $p) {
            $parent = $p->parentNode;
            while ($p->firstChild) {
                $parent->insertBefore($p->firstChild, $p);
            }
            $parent->removeChild($p);
        }

        // Return inner HTML of body
        $body = $doc->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $doc->saveHTML($child);
        }

        return trim($innerHTML);
    }


   public function allQueries(Request $request)
    {
        $query = Enquiry::with('user');

        // 🔍 Search by mobile
        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function($q) use ($search) {
                $q->where('mobile', 'like', "%{$search}%");
            });
        }

        // 🔽 Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $enquiries = $query->latest()->paginate(10);

        return view('admin.enquiry.index', compact('enquiries'));
    }

    public function answerQuery(Request $request)
    {
        $request->validate([
            'query_id' => 'required|exists:enquiries,id',
            'answer'   => 'required|string',
        ]);

        $enquiry = Enquiry::findOrFail($request->query_id);

        $enquiry->update([
            'answer' => $request->answer,
            'status' => 'answered',
            'answered_at' => now()
        ]);

        return response()->json([
            'success' => 'Answer submitted successfully.'
        ]);
    }

    public function deleteQuery(Request $request)
    {
        $request->validate([
            'query_id' => 'required|exists:enquiries,id',
        ]);

        Enquiry::findOrFail($request->query_id)->delete();

        return response()->json(['success' => true]);
    }


}
