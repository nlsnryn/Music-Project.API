<?php

namespace App\Http\Controllers\API;

use App\Models\Song;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Song\StoreSongRequest;

class SongController extends Controller
{
    public function index(int $user_id)
    {
        try {
            $songs = [];
            $songs_by_user = Song::where('user_id', $user_id)->get();
            $user = User::findOrFail($user_id);

            foreach ($songs_by_user as $song) {
                array_push($songs, $song);
            }

            return response()->json([
                'artist_id' => $user->id,
                'artist_name' => $user->first_name . " " . $user->last_name,
                'songs' => $songs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in SongController.index',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function store(StoreSongRequest $request)
    {
        try {
            $file = $request->file;

            if (empty($file)) {
                return response()->json('No song uploaded', 200);
            }

            $user = User::findOrFail($request->get('user_id'));
            $song = $file->getClientOriginalName();
            $file->move('songs/' . $user->id, $song);

            Song::create([
                'user_id' => $request->get('user_id'),
                'title' => $request->get('title'),
                'song' => $song
            ]);

            return response()->json('Song uploaded', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in SongController.store',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, int $user_id)
    {
        try {
            $song = Song::findOrFail($id);

            $currentSong = public_path() . "/songs/" . $user_id . "/" . $song->song;

            if (file_exists($currentSong)) {
                unlink($currentSong);
            }

            if ($song->user_id == $user_id) {
                $song->delete();
            } else {
                return response()->json('Unauthorized', 401);
            }

            return response()->json('Song deleted', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in SongController.store',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
