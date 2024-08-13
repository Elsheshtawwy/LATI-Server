<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServerController extends Controller
{

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'name' => ['required', 'string'],
        ]);
        $code = $this->generateCode();
        auth()->user()->servers()->create([
            'code' => $code,
            'name' => $inputs['name']
        ]);
        return response()->json([
            'message' => 'server created successfully',
            'code' => $code
        ]);
    }

    private function generateCode(): string
    {
        $code = Str::random(6);
        if (Server::where('code', '=', $code)->exists()) {
            $this->generateCode();
        }
        return $code;
    }

    public function index()
    {
        $servers = auth()->user()->servers;
        return response()->json([
            'data' => $servers
        ]);
    }
    
    public function show($id)
    {
        $server = auth()->user()->servers()->find($id);
    
        if (!$server) {
            return response()->json(['error' => 'Server not found'], 404);
        }
    
        return response()->json([
            'data' => $server
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $server = auth()->user()->servers()->find($id);
    
        if (!$server) {
            return response()->json(['error' => 'Server not found'], 404);
        }
    
        $inputs = $request->validate([
            'name' => ['required', 'string'],
        ]);
    
        $server->update([
            'name' => $inputs['name']
        ]);
    
        return response()->json([
            'message' => 'Server updated successfully',
            'data' => $server
        ]);
    }
    
    public function destroy($id)
    {
        $server = auth()->user()->servers()->find($id);
    
        if (!$server) {
            return response()->json(['error' => 'Server not found'], 404);
        }
    
        $server->delete();
    
        return response()->json([
            'message' => 'Server deleted successfully'
        ]);
    }
    

}
