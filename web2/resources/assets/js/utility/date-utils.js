'use strict';

let dateTimeFormat = new Intl.DateTimeFormat('en-US', {year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'});

export default {
    toDateTimeStr(date) {
        let tmpDate = date;
        if (typeof date === 'string') {
            tmpDate = new Date(date);
        } else if (typeof date === 'object' && date instanceof Date) {
            // oke
        } else {
            throw "invalid date input";
        }
        return dateTimeFormat.format(tmpDate);
    }
};