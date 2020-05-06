describe("Event", function(){

    describe("When an email has been entered", function() {
        it("Should check for RFC5322 compliant addresses", function() {
            // arrange - set up variables
            let email_not_real_1 = "notReal";  //no domain included
            let email_not_real_2 = "fake..name@oregonstate.edu";  //consecutive periods not allowed
            let email_not_real_3 = ".fakename@oregonstate.edu";  //first char period not allowed
            let email_not_real_4 = "fakename.@oregonstate.edu";  //last char period not allowed
            let email_not_real_5 = "fake@name@oregonstate.edu";  //@ sign in local part not allowed
            let email_real_and_osu_1 = "fakename@oregonstate.edu";
            let email_real_and_osu_2 = "fake.name@oregonstate.edu";
            let email_real_and_osu_3 = "hi!#$%^&*+_-/=?'`.{1234567890|}~bye@oregonstate.edu";

            // act - call the SUT
            const negative_result_1 = validateEmail(email_not_real_1);
            const negative_result_2 = validateEmail(email_not_real_2);
            const negative_result_3 = validateEmail(email_not_real_3);
            const negative_result_4 = validateEmail(email_not_real_4);
            const negative_result_5 = validateEmail(email_not_real_5);
            
            const positive_result_1 = validateEmail(email_real_and_osu_1);
            const positive_result_2 = validateEmail(email_real_and_osu_2);
            const positive_result_3 = validateEmail(email_real_and_osu_3);
            
            // assert - Check that the results are what we expect
            expect(negative_result_1).toBe(false);
            expect(negative_result_2).toBe(false);
            expect(negative_result_3).toBe(false);
            expect(negative_result_4).toBe(false);
            expect(negative_result_5).toBe(false);

            expect(positive_result_1).toBe(email_real_and_osu_1);
            expect(positive_result_2).toBe(email_real_and_osu_2);
            expect(positive_result_3).toBe(email_real_and_osu_3);
        });

        it("Should validate that the domain is oregonstate.edu or eecs.oregonstate.edu", function() {
            // arrange
            let not_valid_domain = "fakename@gmail.com";
            let osu_domain = "fakename@oregonstate.edu";
            let eecs_domain = "fakename@eecs.oregonstate.edu";

            // act
            const result_not_valid = validateEmail(not_valid_domain);
            const result_osu = validateEmail(osu_domain);
            const result_eecs = validateEmail(eecs_domain);

            // assert
            expect(result_not_valid).toBe(false);
            expect(result_osu).toBe(osu_domain);
            expect(result_eecs).toBe(eecs_domain);
        });
    });

    //Looping test structure referenced from:
    //    https://tosbourn.com/using-loops-in-jasmine/
    //    https://sinaru.com/2017/02/01/careful-using-loops-jasmine-specs/
    describe("When time is converted to double digit format", function() {
        function zero_to_nine_tester(number) {
            it("Should add a leading zero to " + number, function() {
                //console.log('Value tested: ' + number);
                let result = ConvertNumberToTwoDigitString(number);
                let num_string = number.toString();
                let zero = '0';
                let test_result = zero.concat(num_string);
                //console.log('Test result = ' + test_result);
                expect(result).toEqual(test_result);
            });    
        }
        for (let i = 0; i <= 9; i++) {
            //console.log('Iterator set to: ' + i);
            zero_to_nine_tester(i);
        }
        
        // test up to 23 to account for 24hr time: 00 - 23
        function ten_to_23_tester(number) {
            it("Should not add a leading zero to " + number, function() {
                //console.log('Value tested: ' + number);
                let result = ConvertNumberToTwoDigitString(number);
                let num_string = number.toString();
                expect(result).toEqual(num_string);
            });        

        }
        for (let i = 10; i <= 23; i++) {
            //console.log('Iterator set to: ' + i);
            ten_to_23_tester(i);
        }
    });

    describe("When Full Calendar list view is created", function() {
        it("Should call FullCalendar.Calendar", function() {
            // arrange

            // act
            //generateList();
            
            // assert
            expect(true).toEqual(true);

        });

        it("Should list the events for the week", function() {
            // arrange

            // act

            // assert

        });

        it("Should have the current date as the defaultDate", function() {
            // arrange

            // act

            // assert

        });

        it("Should call dateClick from Full Calendar", function() {
            // arrange

            // act

            // assert

        });

        it("Should call calendar.rendar", function() {
            // arrange

            // act

            // assert

        });


    });


});

























