TestCase("general", {
    "test greet": function () {
        assertEquals(true, xajax.on("test"));
    },
    "test Exception": function () {
        try {
            assertException("callback for docReady(fn) must be a function", xajax.onDocReady("test"));
        } catch (exception) {
        }
    }
});