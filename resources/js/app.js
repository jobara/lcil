import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";

import confirmPassword from "./confirmPassword.js";
import dateInput from "./dateInput.js";
import duration from "./duration.js";
import "./tiptap";

Alpine.data("confirmPassword", confirmPassword);
Alpine.data("dateInput", dateInput);
Alpine.data("duration", duration);
Alpine.plugin(focus);

Alpine.start();
