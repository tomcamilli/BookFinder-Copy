import * as tslib_1 from "tslib";
import { Component } from '@angular/core';
import { Http } from '@angular/http';
var HomePage = /** @class */ (function () {
    function HomePage(http) {
        this.http = http;
        this.items = [];
        this.callNumber_one = ""; //This value comes from the input for first call number
        //call this using "this.callNumber_one"
        this.callNumber_two = ""; //This value comes from the input for second call number
        //call this using "this.callNumber_two"
        this.callNumber_three = ""; //This value comes from the input for third call number
        //call this using "this.callNumber_three"
        //These values come from the dropdown menu below each input bar.  They indicate
        //which collection the desired book is located within.  They are set to "General
        //Collection" by default.
        this.collection_one = "General Collection";
        this.collection_two = "General Collection";
        this.collection_three = "General Collection";
        this.theMessage = '<?php echo $teststr ?>';
        this.http = http;
    }
    HomePage.prototype.getCollection = function () {
        //console.log(this.collection_one);
    };
    HomePage.prototype.dummy = function () {
        this.load;
        console.log(this.items);
    };
    //This function normalizes the call numbers in the General Collection, Curriculum Reference Section, and Reference Collection.
    HomePage.prototype.normalize_general = function (call_number) {
        var normal_number = ""; //This string will be the normalized call number
        var sectionCount = 1; //This number counts which section of the number we're reading
        var firstCharSectionThree = false; //This boolean variable checks if we're on the first character of Section 3 of the call number.
        //first, let's remove all the spaces from call_number.  We're going to add
        //them back in as we normalize the call number.
        call_number = call_number.replace(/ /g, "");
        //Now, let's read through the call number character by character.
        for (var i = 0; i < call_number.length; i++) {
            /**
             * Call numbers are divided into sections, which we can number:
             * - Section 1
             * - Section 2
             * - Section 3
             * et cetera.
             * The variable "sectionCount" (defined above) keeps track of which numbered
             * section we are in.  This loop reads through the call number character by
             * character.  It adds each character it finds to the string "normal_number",
             * and whenever it encounters a new section, it adds a space to normal_number.
             */
            //First we get a Number version of the current character.
            var callChar = Number(call_number[i]);
            //All general collection call numbers begin wtih a letter.  So if this call
            //number begins with a number, it's invalid.
            if (isNaN(callChar) == false && i == 0) {
                document.write('Call Number Invalid.');
                break;
            }
            /**
             * Section 1 of a call number is a string of several letters, e.g. "RC".
             * We know we've reached the end of Section 1 when the loop encounters a
             * number for the first time.  So the code below checks if the current
             * character is a letter or not.  If it's a letter, it adds that letter
             * to normal_number.  If it's a number, then it adds a space to normal_number
             * and then adds the number.
             */
            else if (sectionCount == 1) {
                //If the current character is a letter, just add it to normal_number
                if (isNaN(callChar) == true) {
                    normal_number += call_number[i];
                }
                //If the current character is a number, then add a space and then add
                //the current character
                else {
                    normal_number += ' ' + call_number[i];
                    sectionCount++;
                }
            }
            /**
             * Section 2 of a call number is a string of several numbers, e.g. "862".
             * This string of numbers can also have a decimal point in the middle of it,
             * like this: "1140.2".  We know we've reached the end of Section 2 when
             * the loop encounters either a letter OR a decimal point with a letter after
             * it.  So the code below checks if the current character is a number, a
             * decimal point, or a letter, and acts accordingly.
             */
            else if (sectionCount == 2) {
                //If the current character is a number, just add it to normal_number.
                if (isNaN(callChar) == false) {
                    normal_number += call_number[i];
                }
                //If the current character is Not A Number, we need to investigate further.
                else {
                    //Is the current character a decimal point?
                    if (call_number[i] == '.') {
                        //If the current character is a decimal point, we check the character
                        //after it.  If the character after the decimal point is a number, we're
                        //still in Section 2 and don't need to add a space.
                        if (isNaN(Number(call_number[i + 1])) == false) {
                            normal_number += '.';
                        }
                        //Otherwise, the second section is done, and we add a space.
                        else {
                            normal_number += ' ';
                            sectionCount++;
                            firstCharSectionThree = true;
                            //We have not added any characters to section three yet.
                        }
                    }
                    //If the current character is not a decimal point, then it's a letter.
                    else {
                        //If the current character is a letter, then we've moved onto the third
                        //section.
                        normal_number += " ." + call_number[i];
                        sectionCount++;
                    }
                }
            }
            /**
             * Section 3 of a call number is what we call a "Cutter Number".  It consists
             * of a decimal point, followed by a letter, followed by some numbers --
             * e.g. ".A3".  Sometimes, this section will also contain a slash, like this:
             * ".A31952/54".  What's interesting about Section 3 is that it can actually
             * contain multiple cutter numbers, like so: ".G4 .P8".  When a call number
             * contains multiple cutter numbers, each one is separated by a space.  If
             * we ever hit section 4, it will be because the loop finds a letter followed
             * by a decimal point, or two letters in a row.
             */
            else if (sectionCount == 3) {
                //If this is the first character of a cutter number, we add the decimal
                //point and letter.
                if (firstCharSectionThree == true) {
                    normal_number += '.' + call_number[i];
                    firstCharSectionThree = false;
                }
                //If "firstCharSectionThree" is false, then we've already added the letter
                //and can just move on.
                else {
                    //If the current character is a number, then add it to normal_number
                    if (isNaN(callChar) == false) {
                        normal_number += call_number[i];
                    }
                    //If the current character is Not A Number, then we investigate further.
                    else {
                        //If the current character is a slash, 
                        //then add it to normal_number
                        if (call_number[i] == '/') {
                            normal_number += call_number[i];
                        }
                        //If the current character is a decimal point, skip over it
                        else if (call_number[i] == '.') {
                            normal_number += '';
                        }
                        //Otherwise, the current character is a letter and we need to
                        //investigate further.
                        else {
                            //If the character after the current one is a number, we've hit
                            //a second cutter number. Add a space, a decimal point, and then
                            //the character.
                            if (isNaN(Number(call_number[i + 1])) == false) {
                                normal_number += " ." + call_number[i];
                            }
                            //Otherwise, the character after the current one is a decimal point
                            //or a letter, and we've hit Section 4.  Add a space, and then
                            //the character.
                            else {
                                normal_number += " " + call_number[i];
                                sectionCount++;
                            }
                        }
                    }
                }
            }
            /**
             * Section 4 has things like volume number, copy number, et cetera.  These
             * are written as "v.20", "no.40", "c.15", "pt.1", and so on.  If there are
             * multiple of these little numbers, like in "v.20 no.40", we add a space
             * between each of them.
             */
            else {
                //If the current character is a number followed by a letter, we add a space
                if (isNaN(callChar) == false && isNaN(Number(call_number[i + 1])) == true) {
                    normal_number += call_number[i] + ' ';
                }
                //Otherwise, just add the current character
                else {
                    normal_number += call_number[i];
                }
            }
        }
        return normal_number;
    };
    //This function normalizes call numbers in the Children's Collection.
    HomePage.prototype.normalize_childrens = function (call_number) {
        var normal_number = ""; //This string will be the normalized call number
        //first, let's remove all the spaces from call_number.  We're going to add
        //them back in as we normalize the call number.
        call_number = call_number.replace(/ /g, "");
        //Now, let's read through the call number character by character.
        /**
         * The call numbers for children's books come in two forms:
         *  - A number followed by a letter or word (e.g. "398 Pic", "883 C")
         *  - The word "Fict" followed by a last name (e.g. "Fict Bryan")
         * Each of these kinds of numbers can be divided into two sections, separated
         * by a space.  This loop reads through the call number character by
         * character.  It adds each character it finds to the string "normal_number",
         * and whenever it encounters a new section, it adds a space to
         * normal_number.
         */
        //First we get a Number version of the first character.
        var firstChar = Number(call_number[0]);
        //Now, we check whether the call number starts with a number or a letter.
        if (isNaN(firstChar) == false) {
            /**
             * If the call number starts with a number, then this call number has the
             * format "Number followed by word/letter", as described above.  So the
             * loop below reads every character in the call number and adds it to the
             * "normal_number" string.  When it first encounters a letter, it adds a
             * space to normal_number and then adds the character.
             */
            var firstLetter = true; //This boolean variable lets us know when we've
            //encountered a letter for the first time.
            for (var i = 0; i < call_number.length; i++) {
                //First we get a Number version of the current character.
                var callChar = Number(call_number[i]);
                //If we encounter a decimal point, we just add the character.
                if (call_number[i] == '.') {
                    normal_number += call_number[i];
                }
                //If we've encountered a letter for the first time, we add a space
                //to normal_number.
                else if (firstLetter == true && isNaN(callChar) == true) {
                    normal_number += ' ' + call_number[i];
                    firstLetter = false;
                }
                //Otherwise, just add the current character to normal_number.
                else {
                    normal_number += call_number[i];
                }
            }
        }
        else {
            /**
             * If the call number does not start with a number, then it is of the
             * format "Fict [Author Name]".  We add a space after the fourth
             * character.
             */
            for (var i = 0; i < call_number.length; i++) {
                //If this is the fourth character, we add a space after it.
                if (i == 3) {
                    normal_number += call_number[i] + ' ';
                }
                //Otherwise, just add the character.
                else {
                    normal_number += call_number[i];
                }
            }
        }
        return normal_number;
    };
    //This function normalizes call numbers in the Old Textbook Collection and New Textbook Collection.
    HomePage.prototype.normalize_textbook = function (call_number) {
        var normal_number = ""; //This string will be the normalized call number
        var sectionCount = 1; //This number counts which section of the number we're reading
        //first, let's remove all the spaces from call_number.  We're going to add
        //them back in as we normalize the call number.
        call_number = call_number.replace(/ /g, "");
        //All textbook call numbers begin wtih a number.  So if this call
        //number begins with a letter, it's invalid.
        var firstChar = Number(call_number[0]);
        if (isNaN(firstChar) == true) {
            console.log('Call Number Invalid.');
        }
        //Now, let's read through the call number character by character.
        /**
         * The call numbers in the Old Textbook and New Textbook Collection can have
         * up to three sections: A number, a word, and a year - e.g. "374.2 Mer 1966"
         * We'll call the number Section 1, the word Section 2, and the year Section 3.
         * The loop below reads through the call number character by character, adding
         * each character it finds to a string called "normal_number".  Whenever it
         * encounters the start of a new Section, it adds a space before adding the
         * current character.
         */
        for (var i = 0; i < call_number.length; i++) {
            //First we get a Number version of the current character.
            var callChar = Number(call_number[i]);
            /**
             * Section 1 of a call number is a string of numerals, e.g. "372.4".
             * We know we've reached the end of Section 1 when the loop encounters a
             * letter for the first time.  So the code below checks if the current
             * character is a number or not.  If it's a number, it adds that number
             * to normal_number.  If it's a letter, then it adds a space to normal_number
             * and then adds the letter.  Note that the string of numerals can contain
             * a decimal point - so we also have to be on the lookout for those.
             */
            if (sectionCount == 1) {
                //If the current character is a number, just add the character
                if (isNaN(callChar) == false) {
                    normal_number += call_number[i];
                }
                //Otherwise, check to see if it's a decimal point or a letter.
                else {
                    //If the current character is a decimal point, just add it to normal_number
                    if (call_number[i] == '.') {
                        normal_number += call_number[i];
                    }
                    //Otherwise, the character is a letter, and we're done with Section 1.
                    //Add a space, add the current character and increase the SectionCount to 2.
                    else {
                        normal_number += " " + call_number[i];
                        sectionCount++;
                    }
                }
            }
            /**
             * Section 2 of a Textbook call number is a string of letters, e.g. "Sco"
             * We know we've reached the end of Section 2 when the loop encounters a
             * number.  So the code below checks if the current character is a letter
             * or not.  If it's a letter, it adds that letter to normal_number.  If it's
             * a number, then it adds a space to normal_number and then adds the number.
             */
            else if (sectionCount == 2) {
                //If the current character is a letter, just add the character
                if (isNaN(callChar) == true) {
                    normal_number += call_number[i];
                }
                //If the current character is a number, add a space and then add the number
                else {
                    normal_number += ' ' + call_number[i];
                    sectionCount++;
                }
            }
            /**
             * Section 3 of a textbook call number is a string of numerals.  Since there
             * aren't any sections after this, we just add whichever characters are left.
             */
            else {
                normal_number += call_number[i];
            }
        }
        return normal_number;
    };
    HomePage.prototype.normalize = function () {
        var t0 = performance.now(); //Before we normalize, the time is...
        console.log('Original: ' + this.callNumber_one);
        console.log('Collection: ' + this.collection_one);
        var normalized_number = "";
        if (this.collection_one == "General Collection" || this.collection_one == "Curriculum Reference" || this.collection_one == "Reference Collection") {
            normalized_number = this.normalize_general(this.callNumber_one);
        }
        else if (this.collection_one == "Children's Collection") {
            normalized_number = this.normalize_childrens(this.callNumber_one);
        }
        else {
            normalized_number = this.normalize_textbook(this.callNumber_one);
        }
        console.log('Normalized: ' + normalized_number);
    };
    HomePage.prototype.load = function () {
        var _this = this;
        //This is a random variable. Pay no attention to it. It's here because the
        //http.post method needs two variables. It doesn't do anything.
        var randomVariable = 0;
        //This is an HTTP request, which links to a PHP file that queries the database
        //for relevant information.
        this.http.get("http://bookfind.hpc.tcnj.edu/retrieve-data.php", JSON.stringify(randomVariable))
            .subscribe(function (data) {
            _this.data = data["_body"];
            //Right now, we're "alerting" the data - displaying it in a dialog box.
            alert(_this.data);
        }, function (error) {
            //If the connection doesn't work, an error message is sent.
            alert(error);
        });
    };
    HomePage = tslib_1.__decorate([
        Component({
            selector: 'app-home',
            templateUrl: 'home.page.html',
            styleUrls: ['home.page.scss'],
        }),
        tslib_1.__metadata("design:paramtypes", [Http])
    ], HomePage);
    return HomePage;
}());
export { HomePage };
//# sourceMappingURL=home.page.js.map