function workingHoursEmoji(initialProgress) {
    return {
        emoji: "😴",
        progress: initialProgress,

        init() {
            this.$watch("progress", () => this.updateEmoji());
            this.updateEmoji();
        },
        updateEmoji() {
            let newEmoji;
            if (this.progress === 0) newEmoji = "😴";
            else if (this.progress < 25) newEmoji = "🥱";
            else if (this.progress < 50) newEmoji = "🙂";
            else if (this.progress < 75) newEmoji = "😅";
            else if (this.progress >= 100) newEmoji = "🥳";
            else newEmoji = "🎉";

            if (this.emoji !== newEmoji) {
                this.$el.style.transform = "scale(0)";
                setTimeout(() => {
                    this.emoji = newEmoji;
                    this.$el.style.transform = "scale(1)";
                }, 300);
            }
        },
    };
}
