// server.js
import express from "express";
import { spawn } from "child_process";

const app = express();
app.use(express.json());

// OpenAI-compatible endpoint
app.post("/v1/chat/completions", (req, res) => {
    const { model, messages, temperature = 0.7, max_tokens = 512 } = req.body;

    if (!messages || !Array.isArray(messages)) {
        return res.status(400).json({ error: "Invalid messages format" });
    }

    // Prepare prompt from messages
    const prompt = messages.map(m => `${m.role}: ${m.content}`).join("\n");

    // Spawn Qwen CLI process
    const qwen = spawn("qwen", [
        "--model", model,
        "--prompt", prompt
    ]);

    let output = "";
    qwen.stdout.on("data", data => {
        output += data.toString();
    });

    qwen.stderr.on("data", data => {
        console.error("Qwen error:", data.toString());
    });

    qwen.on("close", code => {
        if (code !== 0) {
            return res.status(500).json({ error: "Qwen CLI failed" });
        }

        // Return OpenAI-compatible JSON
        res.json({
            id: "chatcmpl-" + Date.now(),
            object: "chat.completion",
            created: Math.floor(Date.now() / 1000),
            model: model || "Qwen2.5-7B-Instruct",
            choices: [
                {
                    index: 0,
                    message: { role: "assistant", content: output.trim() },
                    finish_reason: "stop"
                }
            ],
            usage: {
                prompt_tokens: prompt.split(/\s+/).length,
                completion_tokens: output.split(/\s+/).length,
                total_tokens: prompt.split(/\s+/).length + output.split(/\s+/).length
            }
        });
    });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`OpenAI-compatible Qwen API running on port ${PORT}`);
});
