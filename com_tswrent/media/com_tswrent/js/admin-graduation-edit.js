
window.addEventListener('DOMContentLoaded', () => {
    
    const durations = document.querySelectorAll('[id$="__duration"]');
    
    durations.forEach(
        input => 
        {
                calcDays(input.id);
        }
    );
});
  
  function calcDays(element){

      var parts = element.split("__"); 
      var firstPart = parts[0];   // jform_graduations
      var secondPart = parts[1];  // graduations0
      var thirdPart = parts[2];    // duration
      var id = "jform_graduations__";
      id =id+secondPart+'__';
      const id_duration = id+'duration'; 
      const id_unit = id+'unit'; 
      const id_days= id+'days';
      
      const duration= document.getElementById(id_duration).value;
      const unit= document.getElementById(id_unit).value;
      let factor= (unit == 0) ?  7 : unit;
      const fullDays = duration*factor;

      document.getElementById(id_days).value = fullDays;
      
  }





(function($){

    $(document).ready(function(){

        /**
         * Berechnet Start- und End-Tag für eine Dauer.
         * - <7 Tage → Einzeltag
         * - >=7 Tage → Wochenintervall (z.B. 7–13, 14–20 ...)
         */
        function getInterval(days) {
            if (days < 7) return {start: days, end: days};
            const week = Math.floor((days - 7) / 7); // erste Woche ab 7
            const start = 7 + week * 7;
            const end = start + 6;
            return {start, end};
        }

        /**
         * Prüft Überschneidungen aller Subform-Zeilen
         */
        function checkGraduationOverlaps() {
            const rows = $('.subform-repeatable-group');
            const intervals = [];

            // Reset: Borders + Tooltips
            rows.css('border', '1px solid #ccc').attr('title', '');

            rows.each(function(index){
                const r = $(this);
                const durationInput = r.find('input[id*="duration"]');
                const unitSelect = r.find('select[id*="unit"]');
                const factorInput = r.find('input[id*="factor"]');

                if (!durationInput.length || !unitSelect.length || !factorInput.length) return;

                let duration = parseInt(durationInput.val()) || 0;
                const unit = parseInt(unitSelect.val()); // 1=day, 0=week
                const factor = parseFloat(factorInput.val()) || 0;

                // Dauer in Tagen
                const days = (unit === 0) ? duration * 7 : duration;

                const interval = getInterval(days);
                intervals.push({start: interval.start, end: interval.end, factor, row: r, index});
            });

            // Überlappungen prüfen
            for (let i = 0; i < intervals.length; i++) {
                for (let j = i + 1; j < intervals.length; j++) {
                    const a = intervals[i];
                    const b = intervals[j];
                    if (a.start <= b.end && a.end >= b.start) {
                        a.row.css('border', '2px solid red');
                        b.row.css('border', '2px solid red');

                        a.row.attr('title', (a.row.attr('title') || '') + `Überlappt mit Zeile ${b.index + 1}\n`);
                        b.row.attr('title', (b.row.attr('title') || '') + `Überlappt mit Zeile ${a.index + 1}\n`);
                    }
                }
            }
        }

        // Live Event-Bindung für Inputs/Selects in Subform
        $(document).on('input change', '.subform-repeatable-container input, .subform-repeatable-container select', checkGraduationOverlaps);

        // MutationObserver für neu hinzugefügte Subform-Zeilen
        const container = document.querySelector('.subform-repeatable-container');
        if (container) {
            const observer = new MutationObserver(() => checkGraduationOverlaps());
            observer.observe(container, { childList: true, subtree: true });
        }

        // Initiale Prüfung, falls bereits Zeilen existieren
        checkGraduationOverlaps();

    });

})(jQuery); 
