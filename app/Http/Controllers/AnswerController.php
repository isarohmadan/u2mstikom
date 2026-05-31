<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Topics;
use App\Models\Answer;
use App\Models\AnswerComment;
use App\Models\AnswerVote;
use App\Models\Announcement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class AnswerController extends Controller
{
    /**
     * Store a newly created answer for a topic.
     */

    public function store(Request $request, Topics $topic)
    {
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string'],
            'images-new' => ['nullable', 'string'],
            'images-old' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput()->with('error', 'Validasi gagal. Periksa input Anda.');
        }

        try {
            DB::beginTransaction();

            // Parse images
            $imagesNew = [];
            $imagesOld = [];
            if ($request->has('images-new')) {
                $imagesNew = json_decode($request->input('images-new'), true) ?? [];
            }
            if ($request->has('images-old')) {
                $imagesOld = json_decode($request->input('images-old'), true) ?? [];
            }

            // Create the answer first to get ID
            $answer = Answer::create([
                'topic_id' => $topic->id,
                'user_id' => auth()->id(),
                'content' => $request->input('content'),
                'is_verified' => false,
                'verified_by' => null,
                'vote_count' => 0,
            ]);

            $updatedContent = $request->input('content');
            $newImageUrls = [];

            // Process images
            if (!empty($imagesNew) || !empty($imagesOld)) {
                // Find images in imagesOld that are NOT in imagesNew - delete them
                foreach ($imagesOld as $oldImageUrl) {
                    if (!in_array($oldImageUrl, $imagesNew)) {
                        // Extract file path from URL
                        $oldPath = $this->extractStoragePath($oldImageUrl);
                        if ($oldPath && FileUploadHelper::exists($oldPath)) {
                            FileUploadHelper::delete($oldPath);
                        }
                    }
                }

                // Move images from temp-answers to answers/{answer_id}/ folder
                foreach ($imagesNew as $imageUrl) {
                    $tempPath = $this->extractStoragePath($imageUrl);
                    
                    if ($tempPath && FileUploadHelper::exists($tempPath)) {
                        // Get filename
                        $filename = basename($tempPath);
                        
                        // New permanent path
                        $permanentPath = "answers/{$answer->id}/{$filename}";
                        
                        // Move file
                        if (FileUploadHelper::move($tempPath, $permanentPath)) {
                            // Generate new URL
                            $newUrl = FileUploadHelper::url($permanentPath);
                            $newImageUrls[$imageUrl] = $newUrl;
                            
                            // Update content HTML with new URL
                            $updatedContent = str_replace($imageUrl, $newUrl, $updatedContent);
                        }
                    } elseif (strpos($imageUrl, 'answers/') !== false) {
                        // Image already in permanent location, keep it
                        $newImageUrls[$imageUrl] = $imageUrl;
                    }
                }

                // Update answer content with new image URLs
                $answer->update(['content' => $updatedContent]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jawaban berhasil ditambahkan',
                    'answer' => $answer,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Jawaban berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan jawaban: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Gagal menambahkan jawaban: ' . $e->getMessage());
        }
    }   

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // Maksimal 2MB
        ]);
        $path = FileUploadHelper::upload($request->file('image'), 'temp-answers');
        return response()->json([
            'success' => true,
            'url' => FileUploadHelper::url($path),
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // Maksimal 10MB
        ]);
        
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;
        
        $path = FileUploadHelper::upload($file, 'temp-answers', $filename);
        
        return response()->json([
            'success' => true,
            'url' => FileUploadHelper::url($path),
            'filename' => $originalName,
            'extension' => $extension,
        ]);
    }

    /**
     * Store a comment for an answer
     */
    public function storeComment(Request $request, $answerId)
    {
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput()->with('error', 'Validasi gagal. Periksa input Anda.');
        }

        try {
            $answer = Answer::findOrFail($answerId);
            
            $comment = AnswerComment::create([
                'answer_id' => $answer->id,
                'user_id' => auth()->id(),
                'content' => $request->input('content'),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Komentar berhasil ditambahkan',
                    'comment' => $comment->load('user'),
                ]);
            }

            return back()->with('success', 'Comment berhasil');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan komentar: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Gagal menambahkan komentar: ' . $e->getMessage());
        }
    }

    /**
     * Vote for an answer (upvote/downvote)
     */
    public function vote(Request $request, $answerId)
    {
        $validator = Validator::make($request->all(), [
            'vote' => ['required', 'in:1,-1'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid vote value',
                ], 422);
            }
            return back()->with('error', 'Invalid vote value');
        }

        try {
            DB::beginTransaction();

            $answer = Answer::findOrFail($answerId);
            $voteValue = (int)$request->vote;

            // Check if user already voted
            $existingVote = AnswerVote::where('answer_id', $answer->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingVote) {
                // If same vote, remove it (toggle off)
                if ($existingVote->vote == $voteValue) {
                    $answer->decrement('vote_count', $voteValue);
                    $existingVote->delete();
                    $action = 'removed';
                } else {
                    // Different vote, update it
                    $answer->increment('vote_count', $voteValue);
                    $answer->decrement('vote_count', $existingVote->vote);
                    $existingVote->vote = $voteValue;
                    $existingVote->save();
                    $action = 'updated';
                }
            } else {
                // New vote
                AnswerVote::create([
                    'answer_id' => $answer->id,
                    'user_id' => auth()->id(),
                    'vote' => $voteValue,
                ]);
                $answer->increment('vote_count', $voteValue);
                $action = 'added';
            }

            $answer->refresh();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vote berhasil',
                    'vote_count' => $answer->vote_count,
                    'action' => $action,
                    'user_vote' => AnswerVote::where('answer_id', $answer->id)
                        ->where('user_id', auth()->id())
                        ->value('vote'),
                ]);
            }

            return back()->with('success', 'Vote berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal vote: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->with('error', 'Gagal vote: ' . $e->getMessage());
        }
    }

    /**
     * Verify/Unverify an answer (for staff/admin only)
     */
    public function verify(Request $request, $answerId)
    {
        if (!auth()->user()->hasAnyRole(['administrator', 'pengurus'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return back()->with('error', 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $answer = Answer::findOrFail($answerId);
            $isCurrentlyVerified = $answer->is_verified;
            
            $answer->update([
                'is_verified' => !$isCurrentlyVerified,
                'verified_by' => !$isCurrentlyVerified ? auth()->id() : null,
            ]);

            $answer->refresh();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $answer->is_verified ? 'Jawaban berhasil diverifikasi' : 'Verifikasi berhasil dihapus',
                    'is_verified' => $answer->is_verified,
                    'verifier' => $answer->verifier ? $answer->verifier->name : null,
                ]);
            }

            return back()->with('success', $answer->is_verified ? 'Jawaban berhasil diverifikasi' : 'Verifikasi berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal verify: ' . $e->getMessage(),
                ], 500);
            }
            
            return back()->with('error', 'Gagal verify: ' . $e->getMessage());
        }
    }

    /**
     * Extract storage path from asset URL
     * Example: http://127.0.0.1:8000/storage/temp-answers/file.png -> temp-answers/file.png
     */
    private function extractStoragePath($url)
    {
        if (empty($url)) {
            return null;
        }

        // Remove domain and /storage/ prefix
        $pattern = '/.*\/storage\//';
        $path = preg_replace($pattern, '', $url);
        
        return $path ?: null;
    }
}
