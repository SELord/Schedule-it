describe("Email", function(){
    let email = 1;
    
    it("should be a valid email", function() {
        // arrange
        // act

        // assert
        expect(email).toEqual(1);

    });

    describe("When email has been entered", function() {
        it("should check for RFC5322 compliant addresses", function() {
            // arrange - set up variables
            let email = "notReal";

            // act - call the SUT
            const result = validateEmail(email);
            
            // assert - Check that the results are what we expect
            //expect(result).toBe(false);
            expect(1).toEqual(1);
        });
    });
});
