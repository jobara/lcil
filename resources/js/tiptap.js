import Alpine from "alpinejs";
import {Editor} from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline";

// Alpine v3 uses proxies for reactive data. In order for the tiptap editor to operate properly with Alpine,
// We need to move the editor outside of Alpines data model.
// See: https://github.com/ueberdosis/tiptap/issues/1515
// The implementation here was inspired by EasterPeanut's workaround
// See: https://github.com/ueberdosis/tiptap/issues/1515#issuecomment-903095273
//      https://codesandbox.io/s/tiptap-with-alpine-js-v3-q4qbp
//      https://github.com/EasterPeanut

// tiptap editor on alpine init
document.addEventListener("alpine:init", () => {
    Alpine.data("editor", (content, options) => {
        let editor;

        return {
            getEditor() {
                return editor;
            },
            updateTabindexes(element) {
                let children = Array.from(element.parentElement?.children ?? []);

                children.forEach((child) => {
                    if (child === element) {
                        child.removeAttribute("tabindex");
                    } else {
                        child.setAttribute("tabindex", -1);
                    }
                });
            },
            focusNext(element) {
                let next = element.nextElementSibling;
                if (next) {
                    next.focus();
                }
            },
            focusPrev(element) {
                let prev = element.previousElementSibling;
                if (prev) {
                    prev.focus();
                }
            },
            focusFirst(element) {
                element.parentElement?.firstElementChild.focus();
            },
            focusLast(element) {
                element.parentElement?.lastElementChild.focus();
            },
            updatedAt: Date.now(),
            content: null,
            init() {
                const model = this;
                let defaults = {
                    element: this.$refs.editorReference,
                    extensions: [
                        Underline,
                        StarterKit.configure({
                            // Disable included extension
                            code: false,
                            codeBlock: false,
                            heading: false
                        })
                    ],
                    content: content,
                    onCreate() {
                        model.updatedAt = Date.now();
                        model.content = this.isEmpty ? "" : this.getHTML();
                    },
                    onUpdate() {
                        model.updatedAt = Date.now();
                        model.content = this.isEmpty ? "" : this.getHTML();
                    },
                    onSelectionUpdate() {
                        model.updatedAt = Date.now();
                    }
                };
                editor = new Editor({...defaults, ...options});
            }
        };
    });
});
