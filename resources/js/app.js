import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";

import confirmPassword from "./confirmPassword.js";
import dateInput from "./dateInput.js";

Alpine.data("confirmPassword", confirmPassword);
Alpine.data("dateInput", dateInput);
Alpine.plugin(focus);

Alpine.start();
