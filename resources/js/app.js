import "../css/app.css";
import "./bootstrap";
import "./cart";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
