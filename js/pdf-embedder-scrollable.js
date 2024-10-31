jQuery(document).ready(function() {
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

    function loadPDF($container) {
        var url = $container.attr('data-url');
        var scale = parseFloat($container.data('scale')) || 1.5; // Use custom scale or default to 1.5
        var loadingTask = pdfjsLib.getDocument(url);

        loadingTask.promise.then(function(pdf) {
            // Loop through each page of the PDF
            for (var pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                renderPage(pdf, pageNum, $container, scale);
            }
        });
    }

    function renderPage(pdf, pageNum, $container, scale) {
        pdf.getPage(pageNum).then(function(page) {
            var viewport = page.getViewport({ scale: scale });

            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Append the canvas to the current container
            $container.append(canvas);

            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext).promise.then(function() {
                // console.log('Page ' + pageNum + ' rendered at scale ' + scale);
            });
        });
    }

    // Find all divs with a 'data-url' attribute and load the corresponding PDF
    jQuery('.pes-pdf-scrollable--viewer[data-url]').each(function() {
        loadPDF(jQuery(this));
    });
});
