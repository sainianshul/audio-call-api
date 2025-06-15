<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Enums\CallStatus;
use Illuminate\Http\Request;

class CallController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/calls/start",
     *     summary="Start a new call",
     *     tags={"Call"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"receiver_id"},
     *             @OA\Property(property="receiver_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Call started successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Call started successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User is already in an active call"
     *     )
     * )
     */
    public function start(Request $request)
    {  
        $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . $request->user()->id,
        ]);

        $user = $request->user();

        if (Call::userHasActiveCall($user->id)) {
            return response()->json([
                'message' => 'User is already in an active call.',
                'data'    => null,
            ], 400);
        }

        $call = Call::startCall($user->id, $request->receiver_id);

        return response()->json([
            'message' => 'Call started successfully.',
            'data'    => $call,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/calls/respond",
     *     summary="Respond to a call",
     *     tags={"Call"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"call_id", "response"},
     *             @OA\Property(property="call_id", type="integer", example=1),
     *             @OA\Property(property="response", type="string", enum={"accepted", "rejected"}, example="accepted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Call response submitted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Call accepted successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to respond to this call"
     *     )
     * )
     */
    public function respond(Request $request)
    {
        $request->validate([
            'call_id'  => 'required|exists:calls,id',
            'response' => 'required|in:accepted,rejected',
        ]);

        $call = Call::findOrFail($request->call_id);

        if ($call->receiver_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to respond to this call.',
                'data'    => null,
            ], 403);
        }

        $status = CallStatus::from($request->response);
        $call->respondToCall($status);

        return response()->json([
            'message' => 'Call ' . $request->response . ' successfully.',
            'data'    => $call,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/calls/end",
     *     summary="End an ongoing call",
     *     tags={"Call"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"call_id"},
     *             @OA\Property(property="call_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Call ended successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Call ended successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to end this call"
     *     )
     * )
     */
    public function end(Request $request)
    {
        $request->validate(['call_id' => 'required|exists:calls,id']);

        $call = Call::findOrFail($request->call_id);

        if (!in_array($request->user()->id, [$call->caller_id, $call->receiver_id])) {
            return response()->json([
                'message' => 'You are not authorized to end this call.',
                'data'    => null,
            ], 403);
        }

        $call->endCall();

        return response()->json([
            'message' => 'Call ended successfully.',
            'data'    => $call,
        ]);
    }
}
