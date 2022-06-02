<?php
        
      //GLOBAL VARIABLES {
      //LIBRARY OF CONGRESS
         //Define global variables for beginning, ending, and user call numbers
         //Each variable represents a section of the call number that must be independently compared
         $LC_BC_Subject;
         $LC_BC_ClassNum;
         $LC_BC_Cutter1;
         $LC_BC_Cutter2;
         $LC_BC_Cutter3;
	 $LC_BC_Year;
         $LC_BC_Version;
         $LC_BC_Copy;

         $LC_EC_Subject;
         $LC_EC_ClassNum;
         $LC_EC_Cutter1;
         $LC_EC_Cutter2;
         $LC_EC_Cutter3;
	 $LC_EC_Year;
         $LC_EC_Version;
         $LC_EC_Copy;

         $LC_CN_Subject;
         $LC_CN_ClassNum;
         $LC_CN_Cutter1;
         $LC_CN_Cutter2;
         $LC_CN_Cutter3;
	 $LC_CN_Year;
         $LC_CN_Version;
         $LC_CN_Copy;

      //MELVIL
         //Define global variables for beginning, ending, and user call numbers
         //Each variable represents a section of the call number that must be independently compared
         $MV_BC_IDNum;
         $MV_BC_IDString;

         $MV_EC_IDNum;
         $MV_EC_IDString;

         $MV_CN_IDNum;
         $MV_CN_IDString;

      //RAND
         $RD_BC_Subject;
         $RD_BC_ClassNum;

         $RD_EC_Subject;
         $RD_EC_ClassNum;

         $RD_CN_Subject;
         $RD_CN_ClassNum;

      //DEWEY DECIMAL
	 $DD_BC_General;
	 $DD_BC_Subject;
	 $DD_BC_Cutter;

	 $DD_EC_General;
	 $DD_EC_Subject;
	 $DD_EC_Cutter;

	 $DD_CN_General;
	 $DD_CN_Subject;
	 $DD_CN_Cutter;
   
//}

   //COMPARISON SECTION {
      /* This section compares the value of the user entered call number to the values of the
       * beginning and ending call numbers for the stack range.  This section is broken up
       * into the different call number systems used within the library with a driver function
       * to determine which function to call. */

      //Driver function that is called by the body of the script.  This function calls either
      //the correct comparison function based on the selected collection.
      function compare($collection) {
         if ($collection == "General Collection" || $collection == "Bound Periodicals" || $collection == "Caldecott" || $collection == "Newberry" || $collection == "REC" || $collection == "Music Reference" || $collection == "Reference Collection" || $collection == "Current Periodicals") {
            return compareLC();
         }
         else if ($collection == "Children's Collection" || $collection == "New Textbook Collection" || $collection == "Old Textbook Collection" || $collection == "Curriculum Reference") {
            return compareMelvil();
         }
         else if ($collection == "RAND") {
            return compareRAND();
         }
         else if ($collection == "Dewey Decimal") {
	    return compareDeweyDecimal();
	 } 
     }

      // Converts a number into a double that is smaller than 1.
      function cutterDouble($num) {
	$numStr = "0.$num";
	return floatval($numStr);
      }

      // Checks if a call number starts with "KFN" and normalizes it if necessary
      function kfnCheck($num) {
	$first = substr($num, 0,5);
	$second = substr($num,5);
	if ($first == "KF  N") {
		//echo $num . "\n";
		return "KFN" . $second;
	}
	return $num;
      }

      //Function that determines whether the user entered call number falls within the call
      //number range for the LC call number system
      function compareLC() {

         /* This function is broken up into two sections, comparison with the beginning call
          * number in the stack range and comparison with the ending call number in the stack
          * range.  This not only helps break up the function into easier chunks, but also
          * improves functionality, as without this breakup it becomes very difficult to
          * determine whether each piece of the call number fits within the range. */

         //Define boolean variables as "checkpoints" to determine whether or not to compare
         //the next piece of the call number
         $beginMatch = false;
         $beginSubjectMatch = false;
         $beginClassMatch = false;
         $beginCutter1Match = false;
         $beginCutter2Match = false;
         $beginCutter3Match = false;
         $beginVersionMatch = false;
         $beginCopyMatch = false;
	 $beginYearMatch = false;

         //Check Subjects: If the subject letter(s) are greater (alphabetically), the entire
         //beginning side is a match.  If they are equal, the beginning subject is a match
         if ($GLOBALS['LC_CN_Subject'] > $GLOBALS['LC_BC_Subject']) {
            $beginMatch = true;
         } else if ($GLOBALS['LC_CN_Subject'] >= $GLOBALS['LC_BC_Subject']) {
            $beginSubjectMatch = true;
         }

         //Check Classification Number: Unlike most libraries. the TCNJ LC call number
         //order treats the classification number as a whole number as opposed to
         //number by number (for example, with the TCNJ system, 400 > 50, with a typical
         //classification number system, 50 > 400 since 5 > 4. Compare the values
         //numerically in the same manner as the subjects.
         if ($beginSubjectMatch) {
            //if(strpos($GLOBALS['LC_CN_ClassNum'], '.') !== false) {
            if ((double) $GLOBALS['LC_CN_ClassNum'] > (double) $GLOBALS['LC_BC_ClassNum']) {
               $beginMatch = true;
            } else if ((double) $GLOBALS['LC_CN_ClassNum'] >= (double) $GLOBALS['LC_BC_ClassNum']) {
               $beginClassMatch = true;
            }
            //}
            /*
            //else {
            $CN_Arr = str_split($GLOBALS['LC_CN_ClassNum']);
            $BC_Arr = str_split($GLOBALS['LC_BC_ClassNum']);
            $EC_Arr = str_split($GLOBALS['LC_EC_ClassNum']);

            $n = 0;

            for ($n; $n < sizeof($CN_Arr); ++$n) {
               if ($n >= sizeof($BC_Arr)) {
                  $beginClassMatch = true;
                  break;
               } else if ($n < sizeof($BC_Arr)) {
                  if ($CN_Arr[$n] > $BC_Arr[$n]) {
                     $beginMatch = true;
                     break;
                  } else if ($CN_Arr[$n] < $BC_Arr[$n]) {
                     break;
                  } else { }
               } else { }
            }

            if ($n == sizeof($CN_Arr)) {
               $beginClassMatch = true;
            }
            //}
            */

            /* Note: The commented code here is for comparing the classification number
             *       as a decimal value the way most classification numbers are compared.
             *       TCNJ's system may change in the future, so this code was left in and
             *       can simply be un-commmented if need be.
             */

            
         }

         //Check Cutter 1: The cutters are compared alphabetically and numerically based
         //on the letter and number of the cutter
         if ($beginClassMatch) {
            //Both the beginning and user call numbers are split up into letters and numbers (which are converted into decimals smaller than 1)
            $LC_CN_Cutter1_Ltr = substr($GLOBALS['LC_CN_Cutter1'], 1, 1);
	    $LC_CN_Cutter1_Num = (int) substr($GLOBALS['LC_CN_Cutter1'], 2);
	    $LC_CN_Cutter1_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter1'], 2));

            $LC_BC_Cutter1_Ltr = substr($GLOBALS['LC_BC_Cutter1'], 1, 1);
            $LC_BC_Cutter1_Num = (int) substr($GLOBALS['LC_BC_Cutter1'], 2);
	    $LC_BC_Cutter1_Dec = (double) cutterDouble(substr($GLOBALS['LC_BC_Cutter1'], 2));

            //If one or both of the call numebrs do not contain a cutter, then the
            //comparison of this section is over. Otherwise, it is continued
            if (empty($GLOBALS['LC_CN_Cutter1']) || empty($GLOBALS['LC_BC_Cutter1'])) {
               $beginCutter1Match = true;
            } else {
               
               //First the letters are compared
               if ($LC_CN_Cutter1_Ltr > $LC_BC_Cutter1_Ltr) {
                  $beginMatch = true;
               } else if (strcmp($LC_CN_Cutter1_Ltr, $LC_BC_Cutter1_Ltr) == 0) {

                  //Then the decimals are compared if the letters are within range
                  if ($LC_CN_Cutter1_Dec > $LC_BC_Cutter1_Dec) {
                     $beginMatch = true;
                  } else if (strcmp($LC_CN_Cutter1_Dec, $LC_BC_Cutter1_Dec) == 0) {
                     $beginCutter1Match = true;
                  }
               }
            }
         }

         //Check Cutter 2: The second and third cutters are compared identically to the
         //first cutter
         if ($beginCutter1Match) {
            $LC_CN_Cutter2_Ltr = substr($GLOBALS['LC_CN_Cutter2'], 0, 1);
            $LC_CN_Cutter2_Num = (int) substr($GLOBALS['LC_CN_Cutter2'], 1);
	    $LC_CN_Cutter2_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter2'], 1));

            $LC_BC_Cutter2_Ltr = substr($GLOBALS['LC_BC_Cutter2'], 0, 1);
            $LC_BC_Cutter2_Num = (int) substr($GLOBALS['LC_BC_Cutter2'], 1);
	    $LC_BC_Cutter2_Dec = (double) cutterDouble(substr($GLOBALS['LC_BC_Cutter2'], 1));

            if (empty($GLOBALS['LC_CN_Cutter2']) || empty($GLOBALS['LC_BC_Cutter2'])) {
               $beginCutter2Match = true;
            } else {
               if ($LC_CN_Cutter2_Ltr > $LC_BC_Cutter2_Ltr) {
                  $beginMatch = true;
               } else if (strcmp($LC_CN_Cutter2_Ltr, $LC_BC_Cutter2_Ltr) == 0) {
                  if ($LC_CN_Cutter2_Dec > $LC_BC_Cutter2_Dec) {
                     $beginMatch = true;
                  } else if (strcmp($LC_CN_Cutter2_Dec, $LC_BC_Cutter2_Dec) == 0) {
                     $beginCutter2Match = true;
                  }
               }
            }
         }

         //Check Cutter 3: The second and third cutters are compared identically to the
         //first cutter
         if ($beginCutter2Match) {
            $LC_CN_Cutter3_Ltr = substr($GLOBALS['LC_CN_Cutter3'], 0, 1);
            $LC_CN_Cutter3_Num = (int) substr($GLOBALS['LC_CN_Cutter3'], 1);
	    $LC_CN_Cutter3_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter3'], 1));

            $LC_BC_Cutter3_Ltr = substr($GLOBALS['LC_BC_Cutter3'], 0, 1);
            $LC_BC_Cutter3_Num = (int) substr($GLOBALS['LC_BC_Cutter3'], 1);
	    $LC_BC_Cutter3_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter3'], 1));

            if (empty($GLOBALS['LC_CN_Cutter3']) || empty($GLOBALS['LC_BC_Cutter3'])) {
               $beginCutter3Match = true;
            } else {
               if ($LC_CN_Cutter3_Ltr > $LC_BC_Cutter3_Ltr) {
                  $beginMatch = true;
               } else if (strcmp($LC_CN_Cutter3_Ltr, $LC_BC_Cutter3_Ltr) == 0) {
                  if ($LC_CN_Cutter3_Dec > $LC_BC_Cutter3_Dec) {
                     $beginMatch = true;
                  } else if (strcmp($LC_CN_Cutter3_Dec, $LC_BC_Cutter3_Dec) == 0) {
                     $beginCutter3Match = true;
                  }
               }
            }
         }

         //Check Version: The version numbers are checked numerically
         if ($beginCutter3Match) {
            //The version number begins with "v.", which is not necessary for comparison
            //purposes and is therefore removed from the strings
            $LC_CN_Version_Num = substr($GLOBALS['LC_CN_Version'], 2);
            $LC_BC_Version_Num = substr($GLOBALS['LC_BC_Version'], 2);

            //From there the numbers are compared numerically
            if (empty($LC_CN_Version_Num)) {
               $beginVersionMatch = true;
            } else if ($LC_CN_Version_Num >= $LC_BC_Version_Num || empty($LC_BC_Version_Num)) {
               $beginMatch = true;
            }
         }

         //Check Copy:  The copy numbers are also just checked numerically
         if ($beginVersionMatch) {
            //Similar to the version number, the copy number begins with c. and this is
            //removed before comparing
            $LC_CN_Copy_Num = substr($GLOBALS['LC_CN_Copy'], 2);
            $LC_BC_Copy_Num = substr($GLOBALS['LC_BC_Copy'], 2);

            //Then the numbers are compared
            if (empty($LC_CN_Copy_Num)) {
               $beginCopyMatch = true;
            } else if ($LC_CN_Copy_Num >= $LC_BC_Copy_Num || empty($LC_BC_Copy_Num)) {
               $beginMatch = true;
            }
         }

	 //Check Year:  The years are checked numerically as well
	 if ($beginMatch) {
	    if (empty($GLOBALS['LC_CN_Year'])) {
	      $beginYearMatch = true;
	    } else if ($GLOBALS['LC_CN_Year'] >= $GLOBALS['LC_BC_Year'] || empty($GLOBALS['LC_BC_CN_Year'])) {
	      $beginMatch = true;
	    }
	 } 

         /* Now that the beginning side has been checked, the ending side must also be
          * checked.  This side is compared exactly the same as the beginning side
          * excepting the comparisons are opposite (i.e. the user call number should be
          * less than the end call number as opposed to greater) */

         //Once again, variables are created as "checkpoints" for the system.  As each
         //piece of the call number is checked, the code first confirms that the last
         //piece was a match before comparing
         $endMatch = false;
         $endSubjectMatch = false;
         $endClassMatch = false;
         $endCutter1Match = false;
         $endCutter2Match = false;
         $endCutter3Match = false;
         $endVersionMatch = false;
         $endCopyMatch = false;

         //Check Subjects
         if ($GLOBALS['LC_CN_Subject'] < $GLOBALS['LC_EC_Subject']) {
            $endMatch = true;
         } else if ($GLOBALS['LC_CN_Subject'] <= $GLOBALS['LC_EC_Subject']) {
            $endSubjectMatch = true;
         }

         //Check Classification Number:  Like the beginning side, the classification
         //numbers are compared as whole number values as opposed to number-by-number
         //The commented code for number-by-number comparison
         if ($endSubjectMatch) {
            //if (strpos($GLOBALS['LC_CN_ClassNum'], '.') !== false) {
               if ((double) $GLOBALS['LC_CN_ClassNum'] < (double) $GLOBALS['LC_EC_ClassNum']) {
                  $endMatch = true;
               } else if ((double) $GLOBALS['LC_CN_ClassNum'] <= (double) $GLOBALS['LC_EC_ClassNum']) {
                  $endClassMatch = true;
               }
               /*
            } else {
               $CN_Arr = str_split($GLOBALS['LC_CN_ClassNum']);
               $EC_Arr = str_split($GLOBALS['LC_EC_ClassNum']);
               $EC_Arr = str_split($GLOBALS['LC_EC_ClassNum']);

               $n = 0;

               for ($n; $n < sizeof($CN_Arr); ++$n) {
                  if ($n >= sizeof($EC_Arr)) {
                     $endClassMatch = true;
                     break;
                  } else if ($n < sizeof($EC_Arr)) {
                     if ($CN_Arr[$n] < $EC_Arr[$n]) {
                        $endMatch = true;
                        break;
                     } else if ($CN_Arr[$n] > $EC_Arr[$n]) {
                        break;
                     } else { }
                  } else { }
               }

               if ($n == sizeof($CN_Arr)) {
                  $endClassMatch = true;
               }
            }
            */
         }

         //Check Cutter 1
         if ($endClassMatch) {
            $LC_CN_Cutter1_Ltr = substr($GLOBALS['LC_CN_Cutter1'], 1, 1);
            $LC_CN_Cutter1_Num = (int) substr($GLOBALS['LC_CN_Cutter1'], 2);
	    $LC_CN_Cutter1_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter1'], 2));

            $LC_EC_Cutter1_Ltr = substr($GLOBALS['LC_EC_Cutter1'], 1, 1);
            $LC_EC_Cutter1_Num = (int) substr($GLOBALS['LC_EC_Cutter1'], 2);
	    $LC_EC_Cutter1_Dec = (double) cutterDouble(substr($GLOBALS['LC_EC_Cutter1'], 2));

            if (empty($GLOBALS['LC_CN_Cutter1']) || empty($GLOBALS['LC_EC_Cutter1'])) {
               $endCutter1Match = true;
            } else {
               if ($LC_CN_Cutter1_Ltr < $LC_EC_Cutter1_Ltr) {
                  $endMatch = true;
               } else if (strcmp($LC_CN_Cutter1_Ltr, $LC_EC_Cutter1_Ltr) == 0) {
                  if ($LC_CN_Cutter1_Dec < $LC_EC_Cutter1_Dec) {
                     $endMatch = true;
                  } else if (strcmp($LC_CN_Cutter1_Dec, $LC_EC_Cutter1_Dec) == 0) {
                     $endCutter1Match = true;
                  }
               }
            }
         }

         //Check Cutter 2
         if ($endCutter1Match) {
            $LC_CN_Cutter2_Ltr = substr($GLOBALS['LC_CN_Cutter2'], 0, 1);
            $LC_CN_Cutter2_Num = (int) substr($GLOBALS['LC_CN_Cutter2'], 1);
	    $LC_CN_Cutter2_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter2'], 1));

            $LC_EC_Cutter2_Ltr = substr($GLOBALS['LC_EC_Cutter2'], 0, 1);
            $LC_EC_Cutter2_Num = (int) substr($GLOBALS['LC_EC_Cutter2'], 1);
	    $LC_EC_Cutter2_Dec = (double) cutterDouble(substr($GLOBALS['LC_EC_Cutter2'], 1));

            if (empty($GLOBALS['LC_CN_Cutter2']) || empty($GLOBALS['LC_EC_Cutter2'])) {
               $endCutter2Match = 1;
            } else {
               if ($LC_CN_Cutter2_Ltr < $LC_EC_Cutter2_Ltr) {
                  $endMatch = true;
               } else if (strcmp($LC_CN_Cutter2_Ltr, $LC_EC_Cutter2_Ltr) == 0) {
                  if ($LC_CN_Cutter2_Dec < $LC_EC_Cutter2_Dec) {
                     $endMatch = true;
                  } else if (strcmp($LC_CN_Cutter2_Dec, $LC_EC_Cutter2_Dec) == 0) {
                     $endCutter2Match = true;
                  }
               }
            }
         }

         //Check Cutter 3
         if ($endCutter2Match) {
            $LC_CN_Cutter3_Ltr = substr($GLOBALS['LC_CN_Cutter3'], 0, 1);
            $LC_CN_Cutter3_Num = (int) substr($GLOBALS['LC_CN_Cutter3'], 1);
	    $LC_CN_Cutter3_Dec = (double) cutterDouble(substr($GLOBALS['LC_CN_Cutter3'], 1));

            $LC_EC_Cutter3_Ltr = substr($GLOBALS['LC_EC_Cutter3'], 0, 1);
            $LC_EC_Cutter3_Num = (int) substr($GLOBALS['LC_EC_Cutter3'], 1);
	    $LC_EC_Cutter3_Dec = (double) cutterDouble(substr($GLOBALS['LC_EC_Cutter3'], 1));

            if (empty($GLOBALS['LC_CN_Cutter3']) || empty($GLOBALS['LC_EC_Cutter3'])) {
               $endCutter3Match = 1;
            } else {
               if ($LC_CN_Cutter3_Ltr < $LC_EC_Cutter3_Ltr) {
                  $endMatch = 1;
               } else if (strcmp($LC_CN_Cutter3_Ltr, $LC_EC_Cutter3_Ltr) == 0) {
                  if ($LC_CN_Cutter3_Dec < $LC_EC_Cutter3_Dec) {
                     $endMatch = true;
                  } else if (strcmp($LC_CN_Cutter3_Dec, $LC_EC_Cutter3_dec) == 0) {
                     $endCutter3Match = true;
                  }
               }
            }
         }

         //Check Version
         if ($endCutter3Match) {
            $LC_CN_Version_Num = substr($GLOBALS['LC_CN_Version'], 2);
            $LC_EC_Version_Num = substr($GLOBALS['LC_EC_Version'], 2);
  
	  if (empty($LC_CN_Version_Num)) {
               $endVersionMatch = true;
            } else if ($LC_CN_Version_Num <= $LC_EC_Version_Num || empty($LC_EC_Version_Num)) {
               $endMatch = true;
            }
         }

         //Check Copy
         if ($endVersionMatch) {
            $LC_CN_Copy_Num = substr($GLOBALS['LC_CN_Copy'], 2);
            $LC_EC_Copy_Num = substr($GLOBALS['LC_EC_Copy'], 2);

            if (empty($LC_CN_Copy_Num)) {
               $endCopyMatch = true;
            } else if ($LC_CN_Copy_Num <= $LC_EC_Copy_Num || empty($LC_EC_Copy_Num)) {
               $endMatch = true;
            }
         }

	 //Check Year
	 if ($endMatch) {
	    if (empty($GLOBALS['LC_CN_Year'])) {
	       $endYearMatch = true;
	    } else if ($GLOBALS['LC_CN_Year'] <= $GLOBALS['LC_EC_Year'] || empty($GLOBALS['LC_EC_Year'])) {
	       $endMatch = true;
	    }
	 }

         /* After the user call number has been checked against the beginning and
          * ending call numbers, the program checks each of the "checkpoint" variables
          * to determine whether the user call number is within the range.  This is
          * broken up just for readbility's sake. */

         //Check for match: If all begin checkpoints are true, begin side is a match
         if($beginSubjectMatch && $beginClassMatch && $beginCutter1Match && $beginCutter2Match && $beginCutter3Match && $beginVersionMatch && $beginCopyMatch) {
            $beginMatch = true;
         }
         //Check for match: If all end checkpoints are true, end side is a match
         if ($endSubjectMatch && $endClassMatch && $endCutter1Match && $endCutter2Match && $endCutter3Match && $endVersionMatch && $endCopyMatch) {
            $endMatch = true;
         }

         //If begin and end sides are matches, range is a match and return 1
         if($beginMatch && $endMatch) {
	    return 1;
         }
         //Otherwise, range isn't a match and return 0
         else {
            return 0;
         }

      }

      //Function that determines whether the user entered call number falls within the call
      //number range for the Melvil call number system
      function compareMelvil() {
         /* This function is broken up into two parts, the beginning side and the ending side.
          * This allows for proper comparison of the call numbers, and makes the code easier
          * to understand. */

         //Variables are defined for the beginning and ending side matches
         $beginMatch = 0;
         $endMatch = 0;

         //The beginning side is compared first.  First the numbers are compared, then the
         //letters.  If both fall within range, the beginning match is true
         if((double)$GLOBALS['MV_CN_IDNum'] == (double)$GLOBALS['MV_BC_IDNum']) {
            if($GLOBALS['MV_CN_IDString'] >= $GLOBALS['MV_BC_IDString']) $beginMatch = 1;
         }
         else if((double)$GLOBALS['MV_CN_IDNum'] > (double)$GLOBALS['MV_BC_IDNum']) $beginMatch = 1;

         //Then the ending side is compared.  As with the beginning side, the numbers are
         //compared first, then the letters
         if((double)$GLOBALS['MV_CN_IDNum'] == (double)$GLOBALS['MV_EC_IDNum']) {
            if ($GLOBALS['MV_CN_IDString'] <= $GLOBALS['MV_EC_IDString']) $endMatch = 1;
         }
         else if ((double)$GLOBALS['MV_CN_IDNum'] <= (double)$GLOBALS['MV_EC_IDNum']) $endMatch = 1;

         //If both sides are a match, the range is a match and return 1.  Otherwise, return 0
         if($beginMatch && $endMatch) return 1;
         else return 0;
      }

      //Function that determines whether the user entered call number falls within the call
      //number range for the RAND collection
      function compareRAND() {
         $beginMatch = false;
         $endMatch = false;

         if($GLOBALS['RD_CN_Subject'] > $GLOBALS['RD_BC_Subject']) $beginMatch = true;
         else if($GLOBALS['RD_CN_Subject'] == $GLOBALS['RD_BC_Subject'] && $GLOBALS['RD_CN_ClassNum'] >= $GLOBALS['RD_BC_ClassNum']) $beginMatch = true;

         if($GLOBALS['RD_CN_Subject'] < $GLOBALS['RD_EC_Subject']) $endMatch = true;
         else if($GLOBALS['RD_CN_Subject'] == $GLOBALS['RD_EC_Subject'] && $GLOBALS['RD_CN_ClassNum'] <= $GLOBALS['RD_EC_ClassNum']) $endMatch = true;

         if($beginMatch && $endMatch) return 1;
         else return 0;
      }

      //Function that determines whether the user entered call number falls within the call
      //number range for the Dewey Decimal collection
      function compareDeweyDecimal() {
	 $beginMatch = false;
	 $beginCutter = false;
	 $endMatch = false;
	 $endCutter = false;

	 if($GLOBALS['DD_CN_General'] > $GLOBALS['DD_BC_General']) $beginCutter = true;
	 else if($GLOBALS['DD_CN_General'] == $GLOBALS['DD_BC_General'] && $GLOBALS['DD_CN_Subject'] >= $GLOBALS['DD_BC_Subject']) $beginCutter = true;

	 //Checks the cutter for the beginning end-cap for the shelf	
	 if($beginCutter == true){
	    $DD_CN_Cutter_Ltr = substr($GLOBALS['DD_CN_Cutter'], 0, 1);
	    $DD_CN_Cutter_Num = (int) substr($GLOBALS['DD_CN_Cutter'], 1);
	    $DD_CN_Cutter_Dec = (double) cutterDouble(substr($GLOBALS['DD_CN_Cutter'], 1));

	    $DD_BC_Cutter_Ltr = substr($GLOBALS['DD_BC_Cutter'], 0, 1);
	    $DD_BC_Cutter_Num = (int) substr($GLOBALS['DD_BC_Cutter'], 1);
	    $DD_BC_Cutter_Dec = (double) cutterDouble(substr($GLOBALS['DD_BC_Cutter'], 1));

	    if (empty($GLOBALS['DD_CN_Cutter']) || empty($GLOBALS['DD_BC_Cutter'])) {
	       $beginMatch = true;
	    } else {
	       if ($DD_CN_Cutter_Ltr > $DD_BC_Cutter_Ltr) {
		   $beginMatch = true;
	       } else if (strcmp($DD_CN_Cutter_Ltr, $DD_BC_Cutter_Ltr) == 0) {
		   $beginMatch = true;
	       }
	    }
	 }

	 //Checks the cutter for the ending end-cap for the shelf
	 if($GLOBALS['DD_CN_General'] < $GLOBALS['DD_EC_General']) $endCutter = true;
	 else if($GLOBALS['DD_CN_General'] == $GLOBALS['DD_EC_General'] && $GLOBALS['DD_CN_Subject'] <= $GLOBALS['DD_EC_Subject']) $endCutter = true;

	 if($endCutter == true){
	    $DD_CN_Cutter_Ltr = substr($GLOBALS['DD_CN_Cutter'], 0, 1);
	    $DD_CN_Cutter_Num = (int) substr($GLOBALS['DD_CN_Cutter'], 1);
	    $DD_CN_Cutter_Dec = (double) cutterDouble(substr($GLOBALS['DD_CN_Cutter'], 1));

	    $DD_EC_Cutter_Ltr = substr($GLOBALS['DD_EC_Cutter'], 0, 1);
	    $DD_EC_Cutter_Num = (int) substr($GLOBALS['DD_EC_Cutter'], 1);
	    $DD_EC_Cutter_Dec = (double) cutterDouble(substr($GLOBALS['DD_EC_Cutter'], 1));
	    
	    if (empty($GLOBALS['DD_CN_Cutter']) || empty($GLOBALS['DD_EC_Cutter'])) {
	       $endMatch = true;
	    } else {
	       if ($DD_CN_Cutter_Ltr < $DD_EC_Cutter_Ltr) {
		   $endMatch = true;
	       } else if (strcmp($DD_CN_Cutter_Ltr, $DD_EC_Cutter_Ltr) == 0) {
		   $endMatch = true;
	       }
	    }	
	 }
	 if($beginMatch && $endMatch) return 1;
	 else return 0;
	 
      }
   //}

   //ASSIGNMENT SECTION {
      /* This section handles the assignment of each of the array values for the beginning, end
       * and user call numbers to the correct global variables.  The importance of this section
       * is to make the comparison easier by defining the sections of each call number and
       * assigning them ahead of time. This section is broken up into a few parts:
       * 1. The assignment of the user call number vs the beginning call number vs the ending call number
       * 2. The assignment of that call number if it uses the Melvil System
       * 3. The assignment of that call number if it uses the LC System */

      //Function that determines what call number system the user's call number uses and calls
      //the assignment for that system
      function assignCN($CN_Arr, $collection) {
         if ($collection == "General Collection" || $collection == "Bound Periodicals" || $collection == "Caldecott" || $collection == "Newberry" || $collection == "REC" || $collection == "Music Reference" || $collection == "Reference Collection" || $collection == "Current Periodicals") {
            assignLC_CN($CN_Arr);
         }
         else if ($collection == "Children's Collection" || $collection == "New Textbook Collection" || $collection == "Old Textbook Collection" || $collection == "Curriculum Reference") {
            assignMelvil_CN($CN_Arr);
         }
         else if($collection == "RAND") {
            assignRAND_CN($CN_Arr);
         }
      }

      //Function that determines what call number system the beginning call number uses and
      //calls the assignment for that system
      function assignBC($BC_Arr, $collection) {
         if ($collection == "General Collection" || $collection == "Bound Periodicals" || $collection == "Caldecott" || $collection == "Newberry" || $collection == "REC" || $collection == "Music Reference" || $collection == "Reference Collection" || 
           $collection == "Current Periodicals") {
            assignLC_BC($BC_Arr);
         }
         else if ($collection == "Children's Collection" || $collection == "New Textbook Collection" || $collection == "Old Textbook Collection" || $collection == "Curriculum Reference") {
            assignMelvil_BC($BC_Arr);
         }
         else if($collection == "RAND") {
            assignRAND_BC($BC_Arr);
         }
      }

      //Function that determines what call number system the ending call number uses and calls
      //the assignment for that system
      function assignEC($EC_Arr, $collection) {
         if ($collection == "General Collection" || $collection == "Bound Periodicals" || $collection == "Caldecott" || $collection == "Newberry" || $collection == "REC" || $collection == "Music Reference" || $collection == "Reference Collection" || $collection == "Current Periodicals") {
            assignLC_EC($EC_Arr);
         }
         else if ($collection == "Children's Collection" || $collection == "New Textbook Collection" || $collection == "Old Textbook Collection" || $collection == "Curriculum Reference") {
            assignMelvil_EC($EC_Arr);
         }
         else if($collection == "RAND") {
            assignRAND_EC($EC_Arr);
         }
      }

      //MELVIL
         //Function that assigns the parts of the users Melvil Call Number to the proper global
         //variables
         function assignMelvil_CN($CN_Arr) {
            //If the first character of the call number is a letter, it will follow the format
            //"Fict" [Author's last name] (ex. Fict Bryan)
            if(ord(substr($CN_Arr[0], 0, 1)) >= 65 && ord(substr($CN_Arr[0], 0, 1)) <= 90) {
               //The IDNum is set to 9999 in this case, since all "Fict" call numbers are shelved
               //after the regular Melvil call numbers, and 9999 is greater than all regular
               //Melvil ID Numbers
               $GLOBALS['MV_CN_IDNum'] = "9999";
               //The IDString is set to the authors name, since books in this section are sorted by
               //authors last name
               $GLOBALS['MV_CN_IDString'] = $CN_Arr[0];
            }
            //Otherwise, the string follows the normal Melvil conventions
            else {
               //The IDNum and IDString are updated with the CN_Arr information (of which IDNum
               //will always be first)
               $GLOBALS['MV_CN_IDNum'] = $CN_Arr[0];
               $GLOBALS['MV_CN_IDString'] = $CN_Arr[1];
            }
         }

         //Function that assigns the parts of the beginning Melvil Call Number of a stack range
         //to the proper global variables
         function assignMelvil_BC($BC_Arr) {
            //If the first character of the call number is a letter, it will follow the format
            //"Fict" [Author's last name] (ex. Fict Bryan)
            if(ord(substr($BC_Arr[0], 0, 1)) >= 65 && ord(substr($BC_Arr[0], 0, 1)) <= 90) {
               //The IDNum is set to 9999 in this case, since all "Fict" call numbers are shelved
               //after the regular Melvil call numbers, and 9999 is greater than all regular
               //Melvil ID Numbers 
               $GLOBALS['MV_BC_IDNum'] = "9999";
               $GLOBALS['MV_BC_IDString'] = $BC_Arr[0];
            }
            //Otherwise, the string follows the normal Melvil conventions
            else {
               //The IDNum and IDString are updated with the CN_Arr information (of which IDNum
               //will always be first)
               $GLOBALS['MV_BC_IDNum'] = $BC_Arr[0];
               $GLOBALS['MV_BC_IDString'] = $BC_Arr[1];
            }
         }

         //Function that assigns the parts of the ending Melvil Call Number of a stack range
         //to the proper global variables
         function assignMelvil_EC($EC_Arr) {
            //If the first character of the call number is a letter, it will follow the format
            //"Fict" [Author's last name] (ex. Fict Bryan)
            if(ord(substr($EC_Arr[0], 0, 1)) >= 65 && ord(substr($EC_Arr[0], 0, 1)) <= 90) {
               //The IDNum is set to 9999 in this case, since all "Fict" call numbers are shelved
               //after the regular Melvil call numbers, and 9999 is greater than all regular
               //Melvil ID Numbers
               $GLOBALS['MV_EC_IDNum'] = "9999";
               $GLOBALS['MV_EC_IDString'] = $EC_Arr[0];
            }
            //Otherwise, the string follows the normal Melvil conventions
            else {
               //The IDNum and IDString are updated with the CN_Arr information (of which IDNum
               //will always be first)
               $GLOBALS['MV_EC_IDNum'] = $EC_Arr[0];
               $GLOBALS['MV_EC_IDString'] = $EC_Arr[1];
            }
         }



      //LIBRARY OF CONGRESS
         //Function that assigns the parts of the users LC Call Number to the proper global
         //variables 
         function assignLC_CN($CN_Arr) {
            /* The beginning of this function must be broken up because the size of the array
             * is only determined after the call number is split in the normalization stage.
             * This means that without checks of each size, the program could try and access
             * an index of the array that does not exist and cause and error. */

            //The first piece of the call number is always the subject, so Subject is set
            if(sizeof($CN_Arr) > 0) $GLOBALS['LC_CN_Subject'] = $CN_Arr[0];

            //The second piece of the call number is always the classification number, so
            //ClassNum is set
            if(sizeof($CN_Arr) > 1) $GLOBALS['LC_CN_ClassNum'] = $CN_Arr[1];

            //The third piece of the call number is always the first cutter, so Cutter1 is
            //set
            if(sizeof($CN_Arr) > 2) $GLOBALS['LC_CN_Cutter1'] = $CN_Arr[2];

            /* The remaining places of the call number can vary, so for each of the
             * remaining indices of the array we will check what role the piece plays */

            //For all call numbers containing more than the subject, classification number
            //and first cutter, loop through the remaining values
            if(sizeof($CN_Arr) > 3) {
               for($i = 3; $i < sizeof($CN_Arr); ++$i) {
		  //echo $CN_Arr[$i] . "\n";
                  //If the current piece begins with "V." it is the version number
                  if(substr($CN_Arr[$i], 0, 2) == 'v.' || substr($CN_Arr[$i], 0, 2) == 'V.') {
                     //Update Version with this piece
                     $GLOBALS['LC_CN_Version'] = $CN_Arr[$i];
                  }

                  //If the current piece begins with "C." it is the copy number
                  else if(substr($CN_Arr[$i], 0, 2) == 'c.' || substr($CN_Arr[$i], 0, 2) == 'C.') {
                     //Update Copy with this piece
                     $GLOBALS['LC_CN_Copy'] = $CN_Arr[$i];
                  }

                  //If the current piece begins with a letter (and was not one of the above
                  //two pieces) it is the second cutter
                  else if ((ord(substr($CN_Arr[$i], 0, 1)) >= 65 && ord(substr($CN_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($CN_Arr[$i], 0, 1)) >= 97 && ord(substr($CN_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_CN_Cutter2'] == "") {
                     //Update Cutter2 with this piece
                     $GLOBALS['LC_CN_Cutter2'] = $CN_Arr[$i];
                  }

                  //If the current piece begins with a letter, was not the version or copy
                  //number and the second cutter is already filled, it is the third cutter
                  else if ((ord(substr($CN_Arr[$i], 0, 1)) >= 65 && ord(substr($CN_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($CN_Arr[$i], 0, 1)) >= 97 && ord(substr($CN_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_CN_Cutter3'] == "") {
                     //Update Cutter3 with this piece
                     $GLOBALS['LC_CN_Cutter3'] = $CN_Arr[$i];
                  }

		 
		  //If the current piece is an integer > 1400 and < 2100, it is the year
		  else if (intval(substr($CN_Arr[$i], 0, 4)) > 1400 && intval(substr($CN_Arr[$i], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_CN_Year'] = $CN_Arr[$i];
		  } 
		  //If the next piece is an integer > 1400 and < 2100, it is the year
		  else if (intval(substr($CN_Arr[$i+1], 0, 4)) > 1400  && intval(substr($CN_Arr[$i+1], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_CN_Year'] = $CN_Arr[$i+1];
		  }

                  //Otherwise, the piece is incorrect and should not be a part of the call
                  //number, and the function is ended
                  else {
                     return;
                  }
               }
            }
         }
         
         //Function that assigns the parts of the beginning LC Call Number of a stack range
         //to the proper global variables
         function assignLC_BC($BC_Arr) {
            /* The beginning of this function must be broken up because the size of the array
             * is only determined after the call number is split in the normalization stage.
             * This means that without checks of each size, the program could try and access
             * an index of the array that does not exist and cause and error. */

            //The first piece of the call number is always the subject, so Subject is set
            if(sizeof($BC_Arr) > 0) $GLOBALS['LC_BC_Subject']  = $BC_Arr[0];

            //The second piece of the call number is always the classification number, so
            //ClassNum is set
            if(sizeof($BC_Arr) > 1) $GLOBALS['LC_BC_ClassNum'] = $BC_Arr[1];
            
            //The third piece of the call number is always the first cutter, so Cutter1 is
            //set
            if(sizeof($BC_Arr) > 2) $GLOBALS['LC_BC_Cutter1']  = $BC_Arr[2];
            
            /* The remaining places of the call number can vary, so for each of the
             * remaining indices of the array we will check what role the piece plays */

            //For all call numbers containing more than the subject, classification number
            //and first cutter, loop through the remaining values
            if(sizeof($BC_Arr) > 3) {
               for($i = 3; $i < sizeof($BC_Arr); ++$i) {
                  //echo "BC: {$BC_Arr} ({$BC_Arr[$i]})\n";
		  //If the current piece begins with "V." it is the version number
                  if(substr($BC_Arr[$i], 0, 2) == 'v.' || substr($BC_Arr[$i], 0, 2) == 'V.') {
                     //Update Version with this piece
                     $GLOBALS['LC_BC_Version'] = $BC_Arr[$i];
                  }

                  //If the current piece begins with "C." it is the copy number
                  else if(substr($BC_Arr[$i], 0, 2) == 'c.' || substr($BC_Arr[$i], 0, 2) == 'C.') {
                     //Update Copy with this piece
                     $GLOBALS['LC_BC_Copy'] = $BC_Arr[$i];
                  }

                  //If the current piece begins with a letter (and was not one of the above
                  //two pieces) it is the second cutter
                  else if ((ord(substr($BC_Arr[$i], 0, 1)) >= 65 && ord(substr($BC_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($BC_Arr[$i], 0, 1)) >= 97 && ord(substr($BC_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_BC_Cutter2'] == "") {
                     //Update Cutter2 with this piece
                     $GLOBALS['LC_BC_Cutter2'] = $BC_Arr[$i];
                  }

                  //If the current piece begins with a letter, was not the version or copy
                  //number and the second cutter is already filled, it is the third cutter
                  else if ((ord(substr($BC_Arr[$i], 0, 1)) >= 65 && ord(substr($BC_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($BC_Arr[$i], 0, 1)) >= 97 && ord(substr($BC_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_BC_Cutter3'] == "") {
                     //Update Cutter3 with this piece
                     $GLOBALS['LC_BC_Cutter3'] = $BC_Arr[$i];
                  }

		  //If the current piece is an integer > 1400 and < 2100, then it is a year
		  else if (intval(substr($BC_Arr[$i], 0, 4)) > 1400 && intval(substr($BC_Arr[$i], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_BC_Year'] = $BC_Arr[$i];
		  }
		  //If the next piece is an integer > 1400 and < 2100, then it is a year
		  else if (intval(substr($BC_Arr[$i+1], 0, 4)) > 1400 && intval(substr($BC_Arr[$i+1], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_BC_Year'] = $BC_Arr[$i+1];
		  }
                  
                  //Otherwise, the piece is incorrect and should not be a part of the call
                  //number, and the function is ended
                  else {
                     return;
                  }
               }
            }
         }

         //Function that assigns the parts of the ending LC Call Number of a stack range
         //to the proper global variables
         function assignLC_EC($EC_Arr) {
            /* The beginning of this function must be broken up because the size of the array
             * is only determined after the call number is split in the normalization stage.
             * This means that without checks of each size, the program could try and access
             * an index of the array that does not exist and cause and error. */

            //The first piece of the call number is always the subject, so Subject is set
            if(sizeof($EC_Arr) > 0) $GLOBALS['LC_EC_Subject']  = $EC_Arr[0];
            
            //The second piece of the call number is always the classification number, so
            //ClassNum is set
            if(sizeof($EC_Arr) > 1) $GLOBALS['LC_EC_ClassNum'] = $EC_Arr[1];
            
            //The third piece of the call number is always the first cutter, so Cutter1 is
            //set
            if(sizeof($EC_Arr) > 2) $GLOBALS['LC_EC_Cutter1']  = $EC_Arr[2];

            /* The remaining places of the call number can vary, so for each of the
             * remaining indices of the array we will check what role the piece plays */

            //For all call numbers containing more than the subject, classification number
            //and first cutter, loop through the remaining values
            if(sizeof($EC_Arr) > 3) {
               for($i = 3; $i < sizeof($EC_Arr); ++$i) {
		  //echo "EC: {$EC_Arr} ({$EC_Arr[$i]})\n";
                  //If the current piece begins with "V." it is the version number
                  if(substr($EC_Arr[$i], 0, 2) == 'v.' || substr($EC_Arr[$i], 0, 2) == 'V.') {
                     //Update Version with this piece
                     $GLOBALS['LC_EC_Version'] = $EC_Arr[$i];
                  }

                  //If the current piece begins with "V." it is the version number
                  else if(substr($EC_Arr[$i], 0, 2) == 'c.' || substr($EC_Arr[$i], 0, 2) == 'C.') {
                     //Update Copy with this piece
                     $GLOBALS['LC_EC_Copy'] = $EC_Arr[$i];
                  }  

                  //If the current piece begins with a letter (and was not one of the above
                  //two pieces) it is the second cutter
                  else if ((ord(substr($EC_Arr[$i], 0, 1)) >= 65 && ord(substr($EC_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($EC_Arr[$i], 0, 1)) >= 97 && ord(substr($EC_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_EC_Cutter2'] == "") {
                     //Update Cutter2 with this piece
                     $GLOBALS['LC_EC_Cutter2'] = $EC_Arr[$i];
                  }

                  //If the current piece begins with a letter, was not the version or copy
                  //number and the second cutter is already filled, it is the third cutter
                  else if ((ord(substr($EC_Arr[$i], 0, 1)) >= 65 && ord(substr($EC_Arr[$i], 0, 1)) <= 90) ||
                  (ord(substr($EC_Arr[$i], 0, 1)) >= 97 && ord(substr($EC_Arr[$i], 0, 1)) <= 122) && $GLOBALS['LC_EC_Cutter3'] == "") {
                     //Update Cutter3 with this piece
                     $GLOBALS['LC_EC_Cutter3'] = $EC_Arr[$i];
                  }
          
		  //If the current piece is an integer > 1400 and < 2100, it is the year
		  else if (intval(substr($EC_Arr[$i], 0, 4)) > 1400 && intval(substr($EC_Arr[$i], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_EC_Year'] = $EC_Arr[$i];
		  }
		  //If the next piece is an integer > 1400 and < 2100, it is the year
		  else if (intval(substr($EC_Arr[$i+1], 0, 4)) > 1400 && intval(substr($EC_Arr[$i+1], 0, 4)) < 2100) {
		     //Update Year with this piece
		     $GLOBALS['LC_EC_Year'] = $EC_Arr[$i+1];
		  }
        
                  //Otherwise, the piece is incorrect and should not be a part of the call
                  //number, and the function is ended
                  else {
                     return;
                  }
               }
            }
         }

	 //Function that assigns the parts of the entered Dewey Decimal Call Number of a stack range
	 //to the proper global variables
	 function assignDD_CN($CN_Arr) {

	    //The first piece of the call number is always the general class, so General is set.	    
	    if(sizeof($CN_Arr) > 0) $GLOBALS['DD_CN_General'] = $CN_Arr[0];

	    //The second piece of the call number is always the subject, so Subject is set.
	    if(sizeof($CN_Arr) > 1) $GLOBALS['DD_CN_Subject'] = $CN_Arr[1];

	    //The third piece of the call number is always the cutter, so Cutter is set.
	    if(sizeof($CN_Arr) > 2) $GLOBALS['DD_CN_Cutter'] = $CN_Arr[2];

	    return;
	 }

	 //Function that assigns the parts of the starting Dewey Decimal Call Number of a stack range
	 //to the proper global variables
	 function assignDD_BC($BC_Arr) {

	    //The first piece of the call number is always the general class, so General is set.
	    if(sizeof($BC_Arr) > 0) $GLOBALS['DD_BC_General'] = $BC_Arr[0];

	    //The second piece of the call number is always the subject, so Subject is set.
	    if(sizeof($BC_Arr) > 1) $GLOBALS['DD_CN_Subject'] = $BC_Arr[1];
	
	    //The third piece of the call number is always the cutter, so Cutter is set.
	    if(sizeof($BC_Arr) > 2) $GLOBALS['DD_BC_Cutter'] = $BC_Arr[2];

	    return;
	 }

	 //Function that assigns the parts of the ending Dewey Decimal Call Number of a stack range
	 //to the proper global variables
	 function assignDD_EC($EC_Arr) {

	    //The first piece of the call number is always the general class, so General is set.
	    if(sizeof($EC_Arr) > 0) $GLOBALS['DD_EC_General'] = $EC_Arr[0];

	    //The second piece of the call number is always the subject, so Subject is set.
	    if(sizeof($EC_Arr) > 1) $GLOBALS['DD_EC_Subject'] = $EC_Arr[1];

	    //The third piece of the call number is always the cutter, so Cutter is set.
	    if(sizeof($EC_Arr) > 2) $GLOBALS['DD_EC_Cutter'] = $EC_Arr[2];

	    return;
	 }
      
      //RAND
         function assignRAND_CN($CN_Arr) {
            $GLOBALS['RD_CN_Subject'] = $CN_Arr[0];
            $GLOBALS['RD_CN_ClassNum'] = $CN_Arr[1];
         }

         function assignRAND_BC($BC_Arr) {
            $GLOBALS['RD_BC_Subject'] = $BC_Arr[0];
            $GLOBALS['RD_BC_ClassNum'] = $BC_Arr[1];
         }

         function assignRAND_EC($EC_Arr) {
            $GLOBALS['RD_EC_Subject'] = $EC_Arr[0];
            $GLOBALS['RD_EC_ClassNum'] = $EC_Arr[1];
         }
   //}

   //NORMALIZE SECTION {
      /* This section handles the correction of formatting for the beginning, end
       * and user call numbers.  The importance of this section is to ensure that all call
       * numbers the system analyzes follow the same guidelines.  This makes the assignment
       * and comparison processes possible. This section is broken up into a few parts:
       * 1. The determination of which collection the call number must be normalized
       * 2. The normalization of a call number if it uses the LC System
       * 3. The normalization of a call number if it uses the Melvil System */

      //Function that determines what call number system the user's call number uses and calls
      //the assignment for that system
      function normalize($callNum, $collection) {

         //For LC call number systems
         if ($collection == "General Collection" || $collection == "Bound Periodicals" || $collection == "Caldecott" || $collection == "Newberry" || $collection == "REC" || $collection == "Music Reference" || $collection == "Reference Collection" || $collection == "Current Periodicals") {
            $callNum = normalizeLC($callNum);
	    if ($collection == "Reference Collection") {
		$callNum = kfnCheck($callNum);
	    }
            return $callNum;
         }

         //For Melvil call number systems
         else if ($collection == "Children's Collection" || $collection == "New Textbook Collection" || $collection == "Old Textbook Collection" || $collection == "Curriculum Reference") {
            $callNum = normalizeMelvil($callNum);
            return $callNum;
         }

         //For modified LC call number systems (Current Periodicals)
         /*if ($collection == "Current Periodicals") {
            $callNum = normalizeCurrentPeriodicals($callNum);
            return $callNum;
         }
         */
         //For RAND Collection
         if($collection == "RAND") {
            $callNum = normalizeRAND($callNum);
            return $callNum;
         }

         else {
            echo "Invalid Collection: " . $collection . "\n";
         }
      }

      //Function that updates a call number from the LC System to the correct format
      function normalizeLC($callNum) {
         //Define helping variables
         $cutterNum = 0;
         $str1 = "";
         $str2 = "";

         //Remove all spaces from the string
         $callNum =  str_replace(' ', '', $callNum);

         //Read the second character of the string (the first will always be a letter)
         //If the second character is a number, place a space before the second character
         if(ord(substr($callNum, 1, 1)) >= 48 && ord(substr($callNum, 1, 1)) <= 57) {
            $str1 = substr($callNum, 0, 1);
            $str2 = substr($callNum, 1);
            $callNum = $str1 . ' ' . $str2;
         }

         //Otherwise, place a space after the second character
         else if((ord(substr($callNum, 1, 1)) >= 65 && ord(substr($callNum, 1, 1)) <= 90) ||
         (ord(substr($callNum, 1, 1)) >= 97 && ord(substr($callNum, 1, 1)) <= 122)) {
            $str1 = substr($callNum, 0, 2);
            $str2 = substr($callNum, 2);
            $callNum = $str1 . ' ' . $str2;
         }

         //Continue reading until a period (.) is found
         for($i = 2; $i < strlen($callNum); ++$i) {
         
            //To prevent accidental infinite loops
            if($i > 50)
               break;

            //When a period is found
            if(substr($callNum, $i, 1) == '.' && $cutterNum == 0) {
               //Read the next character. If it is a number, it's still part of the classification number
               //If it is a letter, it's the beginning of the cutter and a space is added before the period
               if((ord(substr($callNum, $i + 1, 1)) >= 65 && ord(substr($callNum, $i + 1, 1)) <= 90) ||
               (ord(substr($callNum, $i + 1, 1)) >= 97 && ord(substr($callNum, $i + 1, 1)) <= 122)) {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $cutterNum = 1;
                  $i += 3;
               }
            }

            //Continue reading in characters.  If a c or a C is found, check the next letter
            if(substr($callNum, $i, 1) == 'c' || substr($callNum, $i, 1) == 'C') {
               //If it's a period, its a copy number and a space is added before the c
               if(substr($callNum, $i + 1, 1) == '.') {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $i += 3;
               }
               //Otherwise, this is a second cutter and a space is added before the c
               else {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $i++;
               }
            }

            //If a v or a V is found, check the next letter
            if(substr($callNum, $i, 1) == 'v' || substr($callNum, $i, 1) == 'V') {
               //If it's a period, its a version number and a space is added before the v
               if(substr($callNum, $i + 1, 1) == '.') {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $i += 3;
               }
               //Otherwise, this is a second cutter and a space is added before the v
               else {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $i++;
               }
            }

            //If a period is found, this is a second cutter, the period should be removed and a space
            //is added before the letter
            if(substr($callNum, $i, 1) == '.' && $cutterNum == 1) {
               $str1 = substr($callNum, 0, $i);
               $str2 = substr($callNum, $i);
               $str2 = substr($str2, 1);
               $callNum = $str1 . ' ' . $str2;
               $i += 2;
            }

            //If any other letter is found, (not c or v), this is a second cutter and a space is
            //added before the letter
            if((ord(substr($callNum, $i, 1)) >= 65 && ord(substr($callNum, $i, 1)) <= 90) ||
            (ord(substr($callNum, $i, 1)) >= 97 && ord(substr($callNum, $i, 1)) <= 122)) {

               //If the letter is a c or a v, it is a cutter beginning with the letter c or v (since
               //it got past the copy or version number check) and a space is added before the letter
               if(!(substr($callNum, $i, 1) == 'v' || substr($callNum, $i, 1) == 'V' ||
               substr($callNum, $i, 1) == 'c' || substr($callNum, $i, 1) == 'C')) {
                  $str1 = substr($callNum, 0, $i);
                  $str2 = substr($callNum, $i);
                  $callNum = $str1 . ' ' . $str2;
                  $i++;
               }
            }

         }
         return $callNum;

      }

      //Function that updates a call number from the Melvil System to the correct format
      function normalizeMelvil($callNum) {
         $callNum = strtoupper($callNum);

         if(substr($callNum, 0, 4) == "FICT") {
            $callNum = substr($callNum, 5);
         }

         return $callNum;
      }

      //Function that updates a call number from the RAND collection to the correct format
      function normalizeRAND($callNum) {
         //Capitalize the call number string
         $callNum = strtoupper($callNum);

         /*
         //Most of the RAND collection is just Letter-Number, however some of them take the
         //format "REF [Subject]" For call numbers, the call number is simply set to P-9999
         //since they all fall on the last shelf
         if(substr($callNum, 0, 3) == "REF") {
            $callNum = "P-9999";
         }
         */

         $callNum =  str_replace('-', ' ', $callNum);

         return $callNum;
      }

      //Function that updates a call number from the Dewey Decimal System to the correct format
      function normalizeDeweyDecimal($callNum) {
	 $callNum = strtoupper($callNum);
	 $callNum = str_replace('.', ' ', $callNum);
	 $callArray = explode(" ", $callNum);
	 $callNum = "{$callArray[0]}.{$callArray[1]} {$callArray[2]}";

	 return $callNum;
      }

      //Function that updates a call number from the Current Periodicals collection to the
      //correct format -- NEEDS IMPLEMENTATION
      function normalizeCurrentPeriodicals($callNum) {
         $callNum = strtoupper($callNum);
         return $callNum;
      }
   //}
   
   //BODY {
      /* The body of the PHP script is the driver for the program, and it has a few key
       * functions.
       * 1. First, the script must connect with the database in order to be able
       *    to access the Library Stacks information
       * 2. The script must also obtain the user call number and collection passed in 
       *    from the home.page.ts file (in Ionic)
       * 3. The script determines which collection to accept information about from the
       *    database (i.e. if the user is looking for a book in the General Collection,
       *    only General Collection rows will be retrieved from the database)
       * 4. The script loops through each row of the database and called the
       *    functions defined above to determine if the current row is a match for the
       *    call number
       * 5. Finally if the current row is a match, the script passes get the location
       *    values to the home.page.ts file */

      //Define some headers for HTTP protocol usage
      header("Content-Type: application/json; charset=UTF-7");
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Headers: Content-Type");

      // Define database connection parameters
      $servername = 'localhost';
      $location   = 'root';
      $password   = 'tomelocate';
      $dbname     = 'libstacks';

      //Attempt to connect to the database
      $conn = mysql_connect($servername, $location, $password) or die("connect failed");
      mysql_select_db($dbname, $conn) or die ("select failed".mysql_error());

      //Get call number and collection information from the home.page.ts file
      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata, true);		
      $callnum = $request["callnum"];
      $collection = $request["collection"];
      $GENCOLSTARTROW = 1;
      $MUSICREFSTARTROW = 252;
      $GOVDOCSTARTROW = 74;//258;
      $RANDSTARTROW = 80;//264;
      $CURRPERIODICALSTARTROW = 82;//266;
      $CHILDCOLSTARTROW = 149;//296;
      $CURRICREFSTARTROW = 168;//315
      $NEWTEXTBOOKSTARTROW = 168;//315;
      $OLDTEXTBOOKSTARTROOW = 172;//319;
      $BOUNDPERIODICALSTARTROW = 329;//322;
      $REFCOLSTARTROW = 454;//440; 447
      $searchStartRow = 0;

      $wrapAroundCounts;
      $currEndCall;
      $currBeginCall;
      $currRange;
      $currSide;
      $currAisleNum;
      $currCollection;
      $bookEndCall;

      //All items in the New Books, Leisure Reading, and Recent and Current Newspaper
      //collections are held on one stack side, so these can be immediately returned
      if($collection == "New Books") {
         print_r("2,4,35,A,New Books");
         exit;
      }
      else if($collection == "Leisure Reading") {
         print_r("2,4,35,A,Leisure Reading");
         exit;
      }
      else if($collection == "Recent Newspapers") {
         print_r("2,4,35,A,Recent Newspapers");
         exit;
      }
      else if($collection == "Current Newspapers") {
         print_r("2,4,36,A,Current Newspapers");
         exit;
      }
      else if ($collection == "Caldecott") {
      	 print_r("2,6,69,A,Caldecott");
	       exit;
      }
      else if ($collection == "Newberry") {
         print_r("2,6,69,B,Newberry");
         exit;
      }
      else if ($collection == "REC") {
	 print_r("4,2,13-14,A-B,REC");
	 exit;
      }
      else if ($collection == "Reference Collection") {
	 // Get the first character of the call number
	 $first = substr($callnum, 0, 5);
	 $second = substr($callnum, 5);
	 if ($first == "KF  N") {
		$callnum = "KFN" . $second;
	 }
      }
      else if ($collection == "Government Documents") {
        //Gets the first character of the call number
        $firstChar = substr($callnum, 0, 1);

        /* If the first character in the call number is a number, it belongs in the docnjc (Dewey Decimal) section
          * If the first character in the call number is a letter, it belongs in the docusc (SuDocs) or docnj (Title) sections
          * These are hard-coded values.
          */
	if (is_numeric($firstChar)) {
	    $callnum = str_replace('.', ' ', $callnum);
	    $callnum = str_replace(':', ' ', $callnum);
            if (strcasecmp($callnum, "9 74 90 A252") >= 0 && strcasecmp($callnum, "9 74 90 I591988-1989") <= 0) {
              //Belongs to row 77 in the database
              print_r("2,5,38,B,Government Documents");
            } else if (strcasecmp($callnum, "9 74 90 I591990-1990 E") >= 0 && strcasecmp($callnum, "9 74 901 H22- H37 1983") <= 0) {
              //Belongs to row 78 in the database
              print_r("2,5,39,A,Government Documents");
            } else if (strcasecmp($callnum, "9 74 901 H371984- H49 1 1979") >= 0 && strcasecmp($callnum, "9 74 901 W927 Y86") <= 0) {
              //Belongs to row 79 in the database
              print_r("2,5,39,B,Government Documents");
            }
        } else {
	    $ncallnum = str_replace(' ', '', $callnum);
	    if (ctype_alpha($ncallnum)) {	
		if (strcasecmp($callnum, "AD  M I S S I O N S R E L E A S E R E S I D E N TS") >= 0 && strcasecmp($callnum, "WO  R K R E L E A S E") <= 0) {
		    // Belongs to row 79 in the database
		    print_r("2,5,39,B,Government Documents");
		}
	    } else {
            	if (strcasecmp($callnum, "A 1.1") >= 0 && strcasecmp($callnum, "GA 1.13: A F M D-93-51") <= 0) {
             	    //Belongs to row 74 in the database
                    print_r("2,5,37,A,Government Documents");
            	} else if (strcasecmp($callnum, "GA 1.13: A F M D-93-58 B R") >= 0 && strcasecmp($callnum, "I 19.53/2: N J-1995 V.1") <= 0) {
              	    //Belongs to row 75 database
                    print_r("2,5,37,B,Government Documents");
            	} else if (strcasecmp($callnum, "I 19.53/2: N J-1997 V.1") >= 0 && strcasecmp($callnum, "SB  A1-11/2.2") <= 0) {
              	    //Belongs to row 76 database
              	    print_r("2,5,38,A,Government Documents");
            	} else if (strcasecmp($callnum, "SB  A1-12:20/3") >= 0 && strcasecmp($callnum, "Y 10.13:979/ P T.2") <= 0) {
             	    //Belongs to row 77 database
              	    print_r("2,5,38,B,Government Documents");
            	} 
       	    }
	}
	 exit;
      }

      //Split the user call number into an array and call the assignment function
      $CN_Arr = explode(' ', $callnum);
      assignCN($CN_Arr, $collection);

      try {
         //Loop through each row of the database table


      $i = -1;
      /* Our database is set up so that each collection is grouped together.
      * In order to make our searches more efficient, we just start our search
      * at the beginning of the collection entered by the user. This switch statement
      * lets us know where in the table we have to start.
      */
      switch($collection) {
        case "General Collection":
          $searchStartRow = $GENCOLSTARTROW;
          break;
        case "Music Reference":
          $searchStartRow = $MUSICREFSTARTROW;
          break;
        case "Government Documents":
          $searchStartRow = $GOVDOCSTARTROW;
          break;
        case "RAND":
          $searchStartRow = $RANDSTARTROW;
          break;
        case "Current Periodicals":
          $searchStartRow = $CURRPERIODICALSTARTROW;
          break;
        case "Children's Collection":
          $searchStartRow = $CHILDCOLSTARTROW;
          break;
        case "Curriculum Reference":
          $searchStartRow = $CURRICREFSTARTROW;
          break;
        case "New Textbook Collection":
          $searchStartRow = $NEWTEXTBOOKSTARTROW;
          break;
        case "Old Textbook Collection":
          $searchStartRow = $OLDTEXTBOOKSTARTROW;
          break;
        case "Bound Periodicals":
          $searchStartRow = $BOUNDPERIODICALSTARTROW;
          break;
        case "Reference Collection":
          $searchStartRow = $REFCOLSTARTROW;
          break;
        default:
          break;
      }
        while (true) {
          //	for ($i = 1; i < 500; $i++) {
            //Reset beginning and ending global variables for each new row
            $GLOBALS['LC_BC_Subject'] = "";
            $GLOBALS['LC_BC_ClassNum'] = "";
            $GLOBALS['LC_BC_Cutter1'] = "";
            $GLOBALS['LC_BC_Cutter2'] = "";
            $GLOBALS['LC_BC_Cutter3'] = "";
            $GLOBALS['LC_BC_Version'] = "";
            $GLOBALS['LC_BC_Copy'] = "";

            $GLOBALS['LC_EC_Subject'] = "";
            $GLOBALS['LC_EC_ClassNum'] = "";
            $GLOBALS['LC_EC_Cutter1'] = "";
            $GLOBALS['LC_EC_Cutter2'] = "";
            $GLOBALS['LC_EC_Cutter3'] = "";
            $GLOBALS['LC_EC_Version'] = "";
            $GLOBALS['LC_EC_Copy'] = "";

            $GLOBALS['MV_BC_IDNum'] = "";
            $GLOBALS['MV_BC_IDString'] = "";

            $GLOBALS['MV_EC_IDNum'] = "";
            $GLOBALS['MV_EC_IDString'] = "";
	    
	          $i++;
            //Get beginning call number
            $qry_db = 'SELECT begin_call FROM LibraryStacks WHERE row_num = ' . ($searchStartRow + $i);
            $row = mysql_query($qry_db); 
            if($row)
               $BC = mysql_fetch_row($row);


            //Get ending call number
            $qry_db = 'SELECT end_call FROM LibraryStacks WHERE row_num = ' . ($searchStartRow + $i);
            $row = mysql_query($qry_db);
            if($row)
               $EC = mysql_fetch_row($row);
   
            //Get collection
            $qry_db = 'SELECT collection FROM LibraryStacks WHERE row_num = ' . ($searchStartRow + $i);
            $row = mysql_query($qry_db);
            if($row) {
               $CO = mysql_fetch_row($row);
	       /* Because our algorithm parses the entire collection the book is in,
	       * We break when we find the Faux collection (collection after reference collection)
	       * at the end of the database. The faux collection is used as a delimiter
	       * for all of the collections, currently.
	       */
	       if ($CO[0] == "Faux Collection") {
	         print_r("x" . ",");
           	 print_r("x" . ",");
           	 print_r("x" . ",");
           	 print_r("x" . ",");
		 print_r("x");
		 //Close the database connection
		 mysql_close($conn);
		 exit();
	       }
            }

            //For all entries under the specified collection
            if($CO[0] == $collection) {

               //Normalize the beginning and ending call numbers
               $beginCall = normalize($BC[0], $CO[0]);
               $endCall = normalize($EC[0], $CO[0]);

               //Split the database beginning and end calls up
               $BC_Arr = explode(' ', $beginCall);
               $EC_Arr = explode(' ', $endCall);
               //Assign the beginning and ending call numbers
               assignBC($BC_Arr, $collection);
               assignEC($EC_Arr, $collection);
	  
               //Call the comparison function to determine if the current row is a match
               $Match = compare($collection);

               //If match found, get stack info
               if($Match) {
                  /* The code inside this conditional are all database queries, and
                   * they get the floor, aisle number, stack number and stack side */
		
		  //echo "Match found!";

                  $qry_db = 'SELECT floor FROM LibraryStacks WHERE end_call = "' . $EC[0] . '"';
                  $result = mysql_query($qry_db);  
                  if($result)
                     $floor = mysql_fetch_row($result);
   
                  $qry_db = 'SELECT aisle_number FROM LibraryStacks WHERE end_call = "' . $EC[0] . '"';
                  $result = mysql_query($qry_db);  
                  if($result)
                     $aisle_num = mysql_fetch_row($result);
   
                  $qry_db = 'SELECT book_range FROM LibraryStacks WHERE end_call = "' . $EC[0] . '"';
                  $result = mysql_query($qry_db);
                  if($result)
                     $range = mysql_fetch_row($result);
   
                  $qry_db = 'SELECT row_num FROM LibraryStacks WHERE end_call = "' . $EC[0] . '"';
                  $result = mysql_query($qry_db);
                  if($result)
                     $ROW = mysql_fetch_row($result);
   
                  $qry_db = 'SELECT side FROM LibraryStacks WHERE end_call = "' . $EC[0] . '"';
                  $result = mysql_query($qry_db);  
                  if($result)
                     $side = mysql_fetch_row($result);
		  
  		$qry_db = 'SELECT end_call FROM LibraryStacks WHERE row_num = ' . ($searchStartRow + $i);
		  $result = mysql_query($qry_db);
		  if($result)
		     $bookEndCall = mysql_fetch_row($result);
		  
		  /* We've noticed cases in which call numbers will wrap around to multiple shelves,
		  * So we do additional checks to see if this occurs by looking at rows after the row
		  * where we've found a match
		  */   		 
		  $wrapAroundCounts = $searchStartRow + $i + 1;
		  
		  // We grab the contents of the next row
		  $qry_db = 'SELECT begin_call FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currBeginCall = mysql_fetch_row($row);

		  $qry_db = 'SELECT end_call FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currEndCall = mysql_fetch_row($row);
		  
		  $qry_db = 'SELECT book_range FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currRange = mysql_fetch_row($row);

		  $qry_db = 'SELECT aisle_number FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currAisleNum = mysql_fetch_row($row);

		  $qry_db = 'SELECT collection FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currCollection = mysql_fetch_row($row);

                  $qry_db = 'SELECT side FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		  $row = mysql_query($qry_db);
		  if ($row)
		    $currSide = mysql_fetch_row($row);


		  // This wraparound check will run until we find a call number in a succeeding row that isn't
		  // the same as the ending call number for this stack
      while ($bookEndCall[0] == $currBeginCall[0]) {

		  $wrapAroundCounts++;
		   
		  /* If we get here, we know the beginning call number of the next row is equal to the end
		  * call num of the match row. So we check to see if the end call num of the next row is also
		  * equal.
		  */
		  if ($bookEndCall[0] == $currEndCall[0]) {					  
		  // If we get into this block, we know that the row after our match row is a full wraparound.
		  // The wraparound could still continue, so we need to check the next row then
		    $qry_db = 'SELECT begin_call FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currBeginCall = mysql_fetch_row($row);

		    if ($bookEndCall[0] != $currBeginCall[0]) {
		      if ($aisle_num[0] != $currAisleNum[0]) {

		      	$aisle_num[0] = $aisle_num[0].$currAisleNum[0];

		      }

		      $range[0] = $range[0].'-'.$currRange[0];
		      $side[0] = $side[0].'-'.$currSide[0];
		      break;
		    }
			
		    // So we grab the next row
		    $qry_db = 'SELECT end_call FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currEndCall = mysql_fetch_row($row);

		    $qry_db = 'SELECT book_range FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currRange = mysql_fetch_row($row);

		    $qry_db = 'SELECT aisle_number FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currAisleNum = mysql_fetch_row($row);

		    $qry_db = 'SELECT collection FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currCollection = mysql_fetch_row($row);

                    $qry_db = 'SELECT side FROM LibraryStacks WHERE row_num = ' . $wrapAroundCounts;
		    $row = mysql_query($qry_db);
		    if ($row)
		      $currSide = mysql_fetch_row($row);
		    // Once we grab the next row, we go back to the top of the loop
		    continue;

		  } else {
		    /* If we get into the while loop but the end call num of the next row is not equal to the end
		    * end call num of the match row, then we just have a wraparound from the end call num of the
		    * match row to the begin call num of the next row. This is where it ends so we augment our
		    * return values in that case
		    */	
		    if ($aisle_num[0] != $currAisleNum[0]) {

		      $aisle_num[0] = $aisle_num[0].$currAisleNum[0];
		    
		    }
		
		    $range[0] = $range[0].'-'.$currRange[0];
		    $side[0] = $side[0].'-'.$currSide[0];
		    break;

		  }

		}

                  //Print the location information (which can be picked up automatically by
                  //the home.page.ts file using the same HTTP Post method used to call this
                  //PHP script)
		  
		              // This also returns the floor, aisle num, range, side, and collection of 
		              // the row the book is in
                  print_r($floor[0] . ",");
                  print_r($aisle_num[0] . ",");
                  print_r($range[0] . ",");
                  print_r($side[0] . ",");
		              print_r($CO[0]);            	
   
                  //Close the database connection
                  mysql_close($conn);

                  //Exit the script
                  exit();
               }
            } 
         }
      }
      
      catch (PDOException $e) {}

   //}

?>

