import { DateTime } from "luxon";

const getUnitText = function (amount, unit, unitTexts = {}) {
    if (!unitTexts[unit]) {
        return unit;
    }

    let text = unitTexts[unit];

    if (typeof(text) === "string") {
        return text;
    }

    if (amount === 1) {
        return text.singular || text.plural || unit;
    }

    return text.plural || text.singular || unit;
};

const toHumanReadable = function (duration, units = [], unitTexts = {}, separator = " ") {
    if (!units.length) {
        return `${duration.as("milliseconds")} ms`;
    }

    let times = duration.toObject();
    let output = [];

    units.forEach((unit, index) => {
        if (times[unit]) {
            let time = Math.round(times[unit]);
            output.push(`${time} ${getUnitText(time, unit, unitTexts)}`);
        }

        if (output.length === 0 && index === units.length - 1) {
            output.push("0 ${unit}");
        }
    });

    return output.join(separator);
};

export default (options = {}) => ({
    start: DateTime.now().toISO(),
    duration: {},
    units: ["days", "hours", "minutes"],
    delay: 60000,
    unitText: {},
    separator: " ",
    setDuration(component) {
        let duration = DateTime.fromISO(this.start).diffNow(component.units).negate();
        component.duration = {
            iso: duration.toISO(),
            text: toHumanReadable(duration, component.units, component.unitText, component.separator)
        };
    },
    init() {
        this.setDuration(this);
        setInterval(() => this.setDuration(this), this.delay);
    },
    ...options
});
