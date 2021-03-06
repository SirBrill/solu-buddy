describe('Formula parsing and validation', function() {

    describe('is_valid_formula(str) function testing', function() {

        it("Should reject empty formulas", function(){
            expect(is_valid_formula("")).toEqual(false);
        });


          it('Should be able to validate a simple formula string', function () {
             expect(is_valid_formula('NaCl')).toEqual(true);
             expect(is_valid_formula('HNa')).toEqual(true);
             expect(is_valid_formula('HCl')).toEqual(true);
             expect(is_valid_formula('FeO')).toEqual(true);
    
          });

        it('Should reject formulas with nonexistent elements', function () {
             expect(is_valid_formula('NaCz')).toEqual(false);
             expect(is_valid_formula('HNm')).toEqual(false);
             expect(is_valid_formula('HCp')).toEqual(false);

          });
    
          it('Should be able to validate a formula with numbers', function () {
             expect(is_valid_formula('H2O')).toEqual(true);
             expect(is_valid_formula('Na2Cl13')).toEqual(true);
             expect(is_valid_formula('H2Cl15')).toEqual(true);
             expect(is_valid_formula('NaCl2H')).toEqual(true);
    
          });
    
          it('Should be able to validate a formula with numbers in front (multiple compounds)', function () {
             expect(is_valid_formula('4H2O')).toEqual(true);
             expect(is_valid_formula('5Na2Cl13')).toEqual(true);
             expect(is_valid_formula('13H2Cl15')).toEqual(true);
             expect(is_valid_formula('200NaCl2H')).toEqual(true);

          });
    
          it('should not reject duplicate elements', function () {
             expect(is_valid_formula('NaHH')).toEqual(true);
             expect(is_valid_formula('4NaHH')).toEqual(true);
             expect(is_valid_formula('Na2H4H5')).toEqual(true);
             expect(is_valid_formula('NaHClH')).toEqual(true);
             expect(is_valid_formula('NaClNa')).toEqual(true);
    
          });
    
          it('should be able to reject insufficient elements', function () {
             expect(is_valid_formula('')).toEqual(false);
             expect(is_valid_formula('na')).toEqual(false);
             expect(is_valid_formula('nahcl')).toEqual(false);
          });
    
          it('should be able to reject formulas with lowercase elements', function () {
             expect(is_valid_formula('Nacl')).toEqual(false);
             expect(is_valid_formula('H2o')).toEqual(false);
             expect(is_valid_formula('h2O')).toEqual(false);
          });

        it('Should be able to reject formulas with bad parenthesis', function(){
           expect(is_valid_formula("(Na(Cl)(NaCl)")).toEqual(false);
        });

        it("It should accept ridiculous ionic formulas.", function(){

            var tests_to_run = 500;
            var total_length = 0;

            var start = window.performance.now();
            
            for(var i = 0; i < tests_to_run; i++){
                var formula = random_formula_w_ionic(40, 2, 50);
                total_length += formula.length;
                expect(is_valid_formula(formula)).toEqual(true);

                /**
                 * If the formula has parenthesis, check that changing
                 * one parenthesis will make it invalid.
                 */
                if(/\(|\)/g.test(formula)){
                    expect(is_valid_formula(formula.replace("(", ")"))).toEqual(false);
                    expect(is_valid_formula(formula.replace(")", "("))).toEqual(false);
                    expect(is_valid_formula(formula.replace(")", ""))).toEqual(false);
                    expect(is_valid_formula(formula.replace("(", ""))).toEqual(false);
                }
            }

            var time = window.performance.now() - start;

            console.log("Running is_valid_formula(str) " + 5*tests_to_run + " times\non formulas with" +
                " an average length of " + total_length/tests_to_run + "\n" +
                "took " + time + " milliseconds to complete.");
            
        });

        it('Should reject formulas that look correct but have illegal elements.', function(){

            var test_cases = ['H2Nz', 'Zz100Hj(NaCl)4'];
            for(var i = 0; i < test_cases.length; i++){
                expect(is_valid_formula(test_cases[i])).toEqual(false);
            }
        });
        
    });

    describe('front_number(str) function testing', function() {

        it('should obtain the formula quantity for a formula string', function () {
            expect(front_number('4NaCl')).toEqual(4);
            expect(front_number('NaCl')).toEqual(1);
            expect(front_number('Na200Cl')).toEqual(1);
            expect(front_number('8999Na200Cl')).toEqual(8999);

        });

        it('should ignore decimals', function () {
            expect(front_number('4.2NaCl')).toEqual(4);
            expect(front_number('.2NaCl')).toEqual(1);
            expect(front_number('10.5Na200Cl')).toEqual(10);
        });

        it('Should ignore negative numbers', function(){
            expect(front_number('-100Na200Cl')).toEqual(1);
            expect(front_number('-10.5Na200Cl')).toEqual(1);

        });

    });

    describe('string_to_compound_segments(str) function testing', function() {

        it('should break down a simple formula into its elements', function () {

            var test1 = 'NaCl';
            expect(string_to_compound_segments(test1).length).toEqual(2);
            expect(string_to_compound_segments(test1)).toEqual(['Na', 'Cl']);

        });

        it('should break down a complex formula into its elements and respective quantities', function () {

            var test2 = '4H2ONa6Cl';
            expect(string_to_compound_segments(test2).length).toEqual(4);
            expect(string_to_compound_segments(test2)).toEqual(['H2', 'O', 'Na6', 'Cl']);

        });

        it('Should return an empty array on a non-matching string', function () {

            var invalid_strings = ['h', '', 'z', '2', '??', '!!', '()'];

            for(var i = 0; i < invalid_strings.length; i++){
                expect(string_to_compound_segments(invalid_strings[i]).length).toEqual(0);
                expect(string_to_compound_segments(invalid_strings[i])).toEqual([]);
            }
        });

    });

    describe('segment_to_pieces(segment) function testing', function() {

        it('should break down a simple element segment into its quantity and element', function () {

            var test1 = 'Cl2';
            expect(segment_to_pieces(test1).length).toEqual(3);
            expect(segment_to_pieces(test1)[0]).toEqual('Cl2');
            expect(segment_to_pieces(test1)[1]).toEqual('Cl');
            expect(segment_to_pieces(test1)[2]).toEqual('2');

        });

        it('should break down a simple element segment without a leading number ' +
            'into its quantity and element', function () {

            var test1 = 'Fe';
            expect(segment_to_pieces(test1).length).toEqual(3);
            expect(segment_to_pieces(test1)[0]).toEqual('Fe');
            expect(segment_to_pieces(test1)[1]).toEqual('Fe');
            expect(segment_to_pieces(test1)[2]).toEqual('');

        });

        it('should break down a simple element segment into its quantity and element', function () {

            var test1 = 'H200';
            expect(segment_to_pieces(test1).length).toEqual(3);
            expect(segment_to_pieces(test1)[0]).toEqual('H200');
            expect(segment_to_pieces(test1)[1]).toEqual('H');
            expect(segment_to_pieces(test1)[2]).toEqual('200');

        });

        it('Should return an empty array if the string is invalid', function(){

            var invalid_strings = ['h', '', 'z', '2', '??', '!!', '()'];

            for(var i = 0; i < invalid_strings.length; i++){
                expect(segment_to_pieces(invalid_strings[i]).length).toEqual(0);
                expect(segment_to_pieces(invalid_strings[i])).toEqual([]);
            }
        });


    });

});

describe('Compound Creation Testing', function(){

    describe('Compound creation', function () {

        it('should be able to create simple compounds', function () {

            var comp1 = string_to_compound('NaCl');
            expect(comp1.components[0].element.symbol).toEqual('Na');
            expect(comp1.components[0].quantity).toEqual(1);

            expect(comp1.components[1].element.symbol).toEqual('Cl');
            expect(comp1.components[1].quantity).toEqual(1);

            expect(comp1.formula).toEqual('NaCl');
            expect(comp1.quantity).toEqual(1);
            expect(comp1.molecular_weight()).toEqual(58.4427);
            expect(comp1.total_molecular_weight()).toEqual(58.4427);


        });

        it('should be able to create compounds of more than 1 quantity', function () {

            var comp1 = string_to_compound('4NaCl');
            expect(comp1.components[0].element.symbol).toEqual('Na');
            expect(comp1.components[0].quantity).toEqual(1);

            expect(comp1.components[1].element.symbol).toEqual('Cl');
            expect(comp1.components[1].quantity).toEqual(1);

            expect(comp1.formula).toEqual('4NaCl');
            expect(comp1.quantity).toEqual(4);
            expect(comp1.molecular_weight()).toEqual(58.4427);
            expect(comp1.total_molecular_weight()).toEqual(233.7708);


        });

        it('should be able to create simple ionic compounds', function () {


            var form1 = '(NH4)2SO4';
            var comp1 = string_to_compound(form1);

            expect(comp1.components[0].element.symbol).toEqual('S');
            expect(comp1.components[0].quantity).toEqual(1);

            expect(comp1.components[1].element.symbol).toEqual('O');
            expect(comp1.components[1].quantity).toEqual(4);

            expect(comp1.sub_compounds[0].components[0].element.symbol).toEqual('N');
            expect(comp1.sub_compounds[0].components[1].element.symbol).toEqual('H');
            expect(comp1.sub_compounds[0].components[1].quantity).toEqual(4);
            expect(comp1.sub_compounds[0].quantity).toEqual(2);

        });

        it('should be able to create complex organic compounds', function () {


            var form1 = 'C3H4OH(COOH)3';
            var comp1 = string_to_compound(form1);

            expect(comp1.components[0].element.symbol).toEqual('C');
            expect(comp1.components[0].quantity).toEqual(3);
            expect(comp1.components[1].element.symbol).toEqual('H');
            expect(comp1.components[1].quantity).toEqual(4);
            expect(comp1.components[2].element.symbol).toEqual('O');
            expect(comp1.components[2].quantity).toEqual(1);
            expect(comp1.components[3].element.symbol).toEqual('H');
            expect(comp1.components[3].quantity).toEqual(1);


            expect(comp1.sub_compounds[0].quantity).toEqual(3);
            expect(comp1.sub_compounds[0].components[0].element.symbol).toEqual('C');
            expect(comp1.sub_compounds[0].components[0].quantity).toEqual(1);
            expect(comp1.sub_compounds[0].components[1].element.symbol).toEqual('O');
            expect(comp1.sub_compounds[0].components[1].quantity).toEqual(1);
            expect(comp1.sub_compounds[0].components[2].element.symbol).toEqual('O');
            expect(comp1.sub_compounds[0].components[2].quantity).toEqual(1);
            expect(comp1.sub_compounds[0].components[3].element.symbol).toEqual('H');
            expect(comp1.sub_compounds[0].components[3].quantity).toEqual(1);

            expect(comp1.sub_compounds[0].sub_compounds.length).toEqual(0);
            expect(comp1.sub_compounds.length).toEqual(1);

            expect(comp1.molecular_weight()).toEqual(192.1232);

            var form2 = 'NH2CH(C4H5N2)COOH';
            var comp2 = string_to_compound(form2);

            expect(comp2.molecular_weight()).toEqual(155.1542);

        });

        it('should be able to create very long compounds', function(){

            var form1 = '100C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)' +
                '3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3'+
                '3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3'+
                '3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3'+
                '3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3C3H4OH(COOH)3';

            expect(is_valid_formula(form1)).toEqual(true);
            var comp = string_to_compound(form1);
            expect(comp.components[0].element.symbol).toEqual('C');
            expect(comp.total_molecular_weight()).toEqual(1019950.8400000001);
            expect(comp.quantity).toEqual(100);
        });

        it('Should calculate the correct molecular weight for the compound', function(){

            /**
             * Building the compound
             */


            for(var i = 0; i < 100; i++){

                var compound = "";
                var molecular_weight = 0;

                for(var e = 0; e < 500; e++){

                    var element = find_element(getRandomElementKey());
                    var qty = random_int(2,500);
                    var molec_weight = element.atomic_weight * qty;

                    compound += element.symbol;
                    compound += qty;
                    molecular_weight += molec_weight;
                }

                /**
                 * Testing that straightforward summation on this end will
                 * correctly equal molecular weight after parsing
                 */
                expect(string_to_compound(compound).molecular_weight()).toEqual(molecular_weight);

                //Reset compound and molecular weight;
                compound = "";
                molecular_weight = 0;
            }

        });

        it('Should be able to reject empty strings or bad compounds by returning a null value.', function(){

            var invalid_formulas = ['naCl', 'Nacl', '', '8', '-5NaCl', '?H2O', 'Hello', '5.5NaCl', '5Na100.2Cl'];

            invalid_formulas.forEach(function(el, i, arr){
               expect(string_to_compound(el)).toBeNull();
            });

        });

    });

    describe('Compound components', function(){

        it('Should be able to create components with large quantities ' +
            'and correctly compute total atomic weight', function () {

            expect(new Compound_Component('Fe', 2).get_component_atomic_weight()).toEqual(111.69);

        });

    });

    describe("Accept ionic formulas", function(){
       it("Should be able to approve of formulas with ionic compounds.", function () {
          expect(is_valid_formula("(NaCl)(H2O)4")).toEqual(true);
       });
    });
    
    describe('string_to_compounds(str) testing', function() {
    
        it('should accept ionic formulas', function () {
            var ionic_compound = '(NaCl)(H2O)4';
            var compounds = string_to_ionic_compounds(ionic_compound);
            expect(compounds.length).toEqual(2);
            expect(compounds[0]).toEqual('(NaCl)');
            var waterqty = compounds[1];
            expect(compounds[1]).toEqual('(H2O)4');

            var water_components = split_sub_compound(waterqty);
            expect(water_components.length).toEqual(4);
            expect(water_components[1]).toEqual('H2O');
            expect(water_components[3]).toEqual('4');
        });

        it('Should return an empty array if nothing is matched.', function(){
            var invalid_strings = ['h', '', 'z', '2', '??', '!!', '()', '(H2O', 'Ha2O', 'NACL$!@$'];

            for(var i = 0; i < invalid_strings.length; i++){
                expect(string_to_ionic_compounds(invalid_strings[i]).length).toEqual(0);
                expect(string_to_ionic_compounds(invalid_strings[i])).toEqual([]);
            }
        });
    });
    
    describe('add_sub_compounds testing', function() {

        var compound = string_to_compound('NaCl');
        var ionic_compound = '(HFe10)13(H2O)4';
        var compounds = string_to_ionic_compounds(ionic_compound);
        var pieces0 = split_sub_compound(compounds[0]);
        var pieces1 = split_sub_compound(compounds[1]);

        it('should work', function(){
            expect(compounds[0]).toEqual(('(HFe10)13'));
            expect(compounds[1]).toEqual(('(H2O)4'));
            expect(pieces0[0]).toEqual('(HFe10)13');
            expect(pieces0[1]).toEqual('HFe10');
            expect(pieces0[3]).toEqual('13');
        });

        it('(H2O)4 splits properly', function(){
            expect(pieces1[0]).toEqual('(H2O)4');
            expect(pieces1[1]).toEqual('H2O');
            expect(pieces1[3]).toEqual('4');
        });

        compound.add_sub_compounds(compounds);

        it('should add formulas to a pre-existing formula.', function () {
            expect(compound.sub_compounds[0].formula).toEqual('13HFe10');
            expect(compound.sub_compounds[1].formula).toEqual('4H2O');
        });
    });

    describe('segments_to_compound_components(segments)', function() {

        it('should make compound_components properly', function(){
            var test1 = ['Na2', 'H2', 'Cl99', 'Fe9'];

            var components = segments_to_compound_components(test1);

            expect(components[0].quantity).toEqual(2);
            expect(components[1].quantity).toEqual(2);
            expect(components[2].quantity).toEqual(99);
            expect(components[3].quantity).toEqual(9);

            expect(components[0].element.symbol).toEqual('Na');
            expect(components[1].element.symbol).toEqual('H');
            expect(components[2].element.symbol).toEqual('Cl');
            expect(components[3].element.symbol).toEqual('Fe');

        });

    });

});
