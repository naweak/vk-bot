<?php
    namespace Models;
    class SmartReplies {
        function exists($question, $reply) {
            return \R::getAll("SELECT * FROM bot_replies WHERE question = ? AND reply = ?", [
                $question,
                $reply
            ]);
        }
        function add ($question, $reply, $questionAuthor, $replyAuthor, $peerId) {
            !empty($question) ? \R::exec("INSERT INTO bot_replies (question, reply, question_author, reply_author, `time`, peer_id) VALUES (?, ?, ?, ?, ?, ?)", [
                $question,
                $reply,
                $questionAuthor,
                $replyAuthor,
                time(),
                $peerId
            ]) : true;
        }
        function getReply ($question) {
            $replies = \R::getAll("SELECT * FROM bot_replies WHERE question = ?", [
                $question
            ]);
            return $replies[array_rand($replies)];
        }
    }