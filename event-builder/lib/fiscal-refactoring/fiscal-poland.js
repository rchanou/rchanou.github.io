/*

 SITE-SPECIFIC SETTINGS (ENTERED INTO CLUB SPEED)

 Poland POSNET Serial Settings are 9600 8N1 (I believe flow control should be OFF but not 100% sure)
 Moxa's IP:port -- 192.168.1.253:8888

 Sweden's Cleancash Serial settings are 9600 8N1 No Flow Control
 Moxa's IP:port -- Not assigned yet

 */

/*

 To test service, use something like the Chrome plugin "postman" to POST to: http://192.168.111.161:8000/fiscal/print

 Header: Content-Type: application/json

 // POSNET (note "testing" flag)
 {"printer":{"type":"posnet","options":{"host":"192.168.111.38","port":9100 }},"check":{"type":"create","id":1234,"date":"2014-067-07T13:34:33Z","lineItems":[{"name":"First Product","quantity":2,"price":"20,00","discount":"10,00","taxName":"A","taxRate":"10,00","taxAmount":"2,00","total":"30,00"},{"name":"Second Product","quantity":1,"price":"10,00","discount":"5,00","taxName":"B","taxRate":"10,00","taxAmount":"1,00","total":"5,00"}],"tax":[{"name":"A","total":"2,00"},{"name":"B","total":"1,00"}],"checkDiscount":0, "totalDiscount": "15,00", "payment":[{"type":"cash","amount":"35,00"}],"cashier":"KSa","customerId":1234,"terminal":"pos1","total":"35,00","totalDue":"0,00","changeDue":"0,00"}}

 // CLEANCASH (note "testing" flag)
 {"printer":{"type":"cleancash","options":{"host":"192.168.111.38","port":9100, "organizationNumber": 12345,"testing": true}},"check":{"type":"create","id":1234,"date":"2014-12-12T13:34:33Z","lineItems":[{"name":"First Product","quantity":2,"price":20,"discount":10,"taxName":"A","taxRate":10,"taxAmount":2,"total":30},{"name":"Second Product","quantity":1,"price":10,"discount":5,"taxName":"B","taxRate":10,"taxAmount":1,"total":5}],"tax":[{"name":"A","total":2},{"name":"B","total":1}],"checkDiscount":0, "totalDiscount": "15,00", "payment":[{"type":"check","amount":30},{"type":"cash","amount":5}],"cashier":"KSa","customerId":1234,"terminal":"pos1","total":35,"totalDue":0,"changeDue":0}}


 #https://github.com/sevos/jposnet/tree/master/posnet/command
 #http://stackoverflow.com/questions/13655614/unicode-to-mazovia-encoding-redundant-char

 Programming Flow for Poland/POSNET

 On transaction completion:
 1. Set error mode
 2. Add each line item (different lines for discounts)
 3. Add total, taxes, payment line/print
 4. Kick drawer

 Handle kicking drawer:
 1. When drawer kick button pressed in Club Speed

 To consider:
 - Voids (does not do)
 - Deposits/partial payments

 INPUT OBJECT:

 printer: {
 type: 'posnet', // cleancash, scu100
 options: {
 host: '127.0.0.1',
 port: 1234,
 testing: true
 }
 },
 check: {
 type: 'create', // void, refund, copy, training, proforma
 id: 1234,
 date: '2014-12-12T13:34:33Z',
 lineItems: [
 { name: 'First Product', quantity: 2, price: "20.00", discount: 10.00, taxName: "A", taxRate: 10, taxAmount: 2.00, total: 30.00 },
 { name: 'Second Product', quantity: 1, price: 10.00, discount: 5.00, taxName: "B", taxRate: 10, taxAmount: 1.00, total: 5.00 }
 ],
 tax: [
 { name: "A", percent: "10.00", "total": "2.00" },
 { name: "B", percent: "10.00", "total": "1.00" }
 ],
 checkDiscount: "0.00",
 totalDiscount: "0.00",
 payment: [
 { type: 'check', amount: 30.00 },
 { type: 'cash', amount: 5.00 },
 ],
 cashier: 'KSa',
 customerId: 1234,
 terminal: 'pos1',
 total: "35.00",
 totalDue: "0.00",
 changeDue: "0.00"
 }

 RETURN OBJECT:

 {
 success: true, // false,
 message: "Maged was too awesome", // Sweden control code to print on receipt'1239dhASDFBA'
 }

 */

var net = require('net')
    , carrier = require('carrier');

// Constants
var cr = Buffer([0x0d]);
var escp = Buffer([0x1b, 0x50]);
var escslash = Buffer([0x1b, 0x5c]);
var printerOptions;

exports.print = function print(input, callback) {

    /**
     * SCU100 -- Default: 9600 8N1 No Flow Control
     */

    function generateChecksum(data) {
        var checksum = 0;

        for(var i = 0; i < data.length; i++) {
            checksum += data[i];
        }

        checksum = checksum ^ 0xff;

        return checksum;
    }

    if(input.printer.type == 'scu100' && (input.check.type == 'create' || input.check.type == 'copy')) {
        var start = new Buffer([0x10, 0x02]);
        var end   = new Buffer([0x10, 0x03]);

        printerOptions = {
            port: input.printer.options.port,
            host: input.printer.options.host,
            testing: input.printer.options.testing || false,
            timeBetweenCommandsInMs: input.printer.options.timeBetweenCommandsInMs || 100,
            timeout: input.printer.options.timeout || 2000,
            signatureCode: null,
            manufacturingIdCode: null,
						terminal: null
        };

        var printer = net.connect(printerOptions);
        printer.setTimeout(printerOptions.timeout);

        printer.on('data', function(data) {
            console.log('Received from fiscal:');
            console.log(data);
        });

        printer.on('end', function() {
            console.log('Fiscal disconnected');
        });

        printer.on('close', function() {
            console.log('Fiscal closed');
        });

        printer.on('timeout', function() {
            console.log('Fiscal timeout');

            var result = {
                success: false,
                message: "Fiscal timeout"
            };
            printer.destroy();
            callback(result);
            return;

        });

        printer.on('error', function(err) {
            console.log('Fiscal error:');
            console.log(err);

            var result = {
                success: false,
                message: err.code
            };

            callback(result);
            printer.destroy();
            return;
        });

        printer.on('connect', function(stream) {

            console.log('Connected to ' + input.printer.type + ' fiscal device at ' + printerOptions.host + ':' + printerOptions.port);

						console.log('Received this command to print:');
						console.log(input);
						
            var my_carrier = carrier.carry(printer);
            my_carrier.on('line',  function(rawData) {
                console.log(rawData);

                // Reply Data Packet
                if(rawData.length == 99 && rawData[18] == 0x80) {
                    if(rawData[19] != 0x30) {
                        // ERROR

                        /*
                         0x30 : NO ERROR
                         0x31 : OPERATION FAILED.
                         0x32 : REGISTER ID MISMATCH
                         0x33 : RECEIPT FORMAT FAIL
                         0x34 : BUFFER SIZE OVER
                         0x35 : PRINTER IS OFF OR BUSY
                         */

                        var err;

                        switch(rawData[19]) {
                            case 0x31:
                                err = "Operation Failed. (Error 0x31)";
                                break;
                            case 0x32:
                                err = "Register ID Mismatch. (Error 0x32)";
                                break;
                            case 0x33:
                                err = "Receipt Format Fail. (Error 0x33)";
                                break;
                            case 0x34:
                                err = "Buffer Size Over. (Error 0x34)";
                                break;
                            case 0x35:
                                err = "Printer Is Off or Busy. (Error 0x35)";
                                break;
                            default:
                                err = "No Error Message Given. (Error 0x" + rawData[19].toString(16) + ")";
                        }
                        console.log('Fiscal error:');
                        console.log(err);
                        console.log('Raw data:');
                        console.log(rawData);

                        var result = {
                            success: false,
                            message: err
                        };
                        printer.destroy();
                        return callback(result);

                    } else {
                        var result = {
                            success: true,
                            message: "",
                            serialCode: rawData.substr(20, 59),
                            receiptCode: rawData.substr(70, 17),
                            organizationNumber: printerOptions.organizationNumber
                        };

                        console.log('Sending success');
                        console.log(result);

                        console.log('Sending Acknowledge and closing fiscal');
                        printer.write(Buffer([0x10, 0x06]));
                        printer.destroy();

                        return callback(null, result);
                    }
                }

            });

            // Send ENQ five times, expect DLE ACK back
            setTimeout(function() {
                printer.write(Buffer([0x05, 0x05, 0x05, 0x05, 0x05]));
            }, printerOptions.timeBetweenCommandsInMs * 0);

            // Send ENQUIRE, expect DLE ACK back
            setTimeout(function() {
                printer.write(Buffer([0x10, 0x05]));
            }, printerOptions.timeBetweenCommandsInMs * 1);

            // Send receipt
            setTimeout(function() {

                if(typeof input.printer.options.runNumber === 'undefined') {
                    var result = {
                        success: false,
                        message: 'No "runNumber" defined in input.printer.options object'
                    };
                    printer.destroy();
                    return callback(result)
                }

                var terminal = input.printer.options.terminal || input.check.terminal; // Terminal can be overridden in options, defaults to check
								var registerId = ("                " + terminal).slice(-16);
                var command = Buffer([0x80]);
                var receiptData = '';

                var checkType;
                switch(input.check.type) {
                    case 'create':
                        checkType = 0x30;
                        break;
                    case 'training':
                        checkType = 0x33;
                        break;
                    case 'copy':
                        checkType = 0x31;
                        break;
                    case 'proforma':
                        checkType = 0x32;
                        break;
                    default:
                        var result = {
                            success: false,
                            message: 'Invalid check type given: ' + input.check.type + ' (should be "create" or "training")'
                        };
                        printer.destroy();
                        return callback(result);
                        break;
                }

                var date = new Date(input.check.date);
                receiptData += date.toISOString().replace(/-/g, '').replace('T', '').replace(/:/g, '').substr(0, 12); // '201406011200'
                receiptData += ('          ' + input.check.id).slice(-10);
                receiptData += registerId; // Cash Register ID
                receiptData += input.printer.options.runNumber || input.check.id; // Run Number
                receiptData += checkType;

                if(input.check.type == 'create') {
                    receiptData += '             '; // Return Amount
                    receiptData += ('             ' + input.check.total.replace(/[^0-9\.]+/g,'')).slice(-13); // Sale Amount
                } else {
                    // TODO Handle Void/Refund?
                }

                // Array to hold values for VAT 1 - 4
                var taxArray = [
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''}
                ];
                input.check.tax.forEach(function(tax) {
                    if(tax.vatId == "1" || tax.vatId == "2" || tax.vatId == "3" || tax.vatId == "4") {
                        taxArray[parseInt(tax.vatId) - 1] = tax;
                    } else {
                        var result = {
                            success: false,
                            message: 'Invalid VAT ID given: ' + tax.vatId + ' (should be "1", "2", "3" or "4")'
                        };
                        printer.destroy();
                        return callback(result);
                    }
                });

                receiptData += ('     ' + taxArray[0].percent).slice(-5); // VAT 1: %
                receiptData += ('             ' + taxArray[0].total.replace(/[^0-9\.]+/g,'')).slice(-13); // VAT 1: Amount
                receiptData += ('     ' + taxArray[1].percent).slice(-5); // VAT 2: %
                receiptData += ('             ' + taxArray[1].total.replace(/[^0-9\.]+/g,'')).slice(-13); // VAT 2: Amount
                receiptData += ('     ' + taxArray[2].percent).slice(-5); // VAT 3: %
                receiptData += ('             ' + taxArray[2].total.replace(/[^0-9\.]+/g,'')).slice(-13); // VAT 3: Amount
                receiptData += ('     ' + taxArray[3].percent).slice(-5); // VAT 4: %
                receiptData += ('             ' + taxArray[3].total.replace(/[^0-9\.]+/g,'')).slice(-13); // VAT 4: Amount

                receiptData = Buffer(receiptData);

                var checksum = Buffer(generateChecksum(receiptData));

                var packet = Buffer.concat([start, registerId, command, receiptData, end, checksum]);

                console.log('Sending Receipt Data ' + command);
                console.log('Packet Length ' + packet.length);
                printer.write(packet);
            }, printerOptions.timeBetweenCommandsInMs * 2);

            if(printerOptions.testing === true) {
                setTimeout(function() {
                    var result = {
                        success: true,
                        message: "",
                        serialCode: '1234567890',
                        receiptCode: '12312312312312312312123123123123123123121231231231231231231',
                        organizationNumber: printerOptions.organizationNumber
                    };

                    console.log('Closing fiscal');
                    printer.destroy();

                    callback(null, result);

                }, printerOptions.timeBetweenCommandsInMs * 3);
            }

        });
    }


    /**
     * CLEANCASH
     */

    if(input.printer.type == 'cleancash') {

        if(typeof input.printer.options.organizationNumber === 'undefined') {
            var result = {
                success: false,
                message: "Organization number not set"
            };

            callback(result);
            return;
        }

        printerOptions = {
            port: input.printer.options.port,
            host: input.printer.options.host,
            testing: input.printer.options.testing || false,
            organizationNumber: input.printer.options.organizationNumber,
            timeBetweenCommandsInMs: input.printer.options.timeBetweenCommandsInMs || 100,
            timeout: input.printer.options.timeout || 3000,
            signatureCode: null,
            manufacturingIdCode: null,
						terminal: null // If provided will override terminal name
        }

        var printer = net.connect(printerOptions);
        printer.setTimeout(printerOptions.timeout);

        printer.on('data', function(rawData) {
            console.log('Received from fiscal:');
            console.log(rawData);
            console.log(Buffer(rawData));

            // BRIAN CUT HERE *START*
            data = rawData.split('#');
            switch(data[3]) {
                case 'IR':
                    console.log('Received Printer Identity');
                    console.log(rawData);
                    printerOptions.manufacturingIdCode = data[4];
                    break;
								case 'ACK':
                    console.log('Received Positive Acknowledgement');
                    console.log(rawData);
                    break;
                case 'NAK':
                    var errorMessages = {
                        '001': 'Invalid LRC',
                        '002': 'Unknown message type',
                        '003': 'Invalid data/parameter',
                        '004': 'Invalid sequence',
                        '005': 'Deprecated (Not used)',
                        '006': 'CleanCash Not operationa',
                        '007': 'Invalid POS ID',
                        '008': 'Internal error',
                        '009': 'License exceeded (CCSP v2)',
                        '010': 'Internal storage full (CCSP v2)',
                        '011': 'Invalid sequence number'
                    }
                    console.log('Fiscal error: ' + errorMessages[data[4]]);

                    var result = {
                        success: false,
                        message: errorMessages[data[4]]
                    };

                    callback(result);
                    printer.destroy();
                    break;
                case 'SR':
                    // Parse out the IR "#!#00D#SR#12312312312312312312123123123123123123121231231231231231231#7D\r"
                    printerOptions.signatureCode = data[4];

                    console.log('Received Signature Code');
                    console.log(rawData);

                    var result = {
                        success: true,
                        message: "",
                        serialCode: printerOptions.manufacturingIdCode,
                        receiptCode: printerOptions.signatureCode,
                        organizationNumber: printerOptions.organizationNumber
                    };

                    console.log('Sending success');
                    console.log(result);

                    console.log('Closing fiscal');
                    printer.destroy();

                    callback(null, result);
                    return;
                    break;
                default:
                    console.log('Caught unhandled message:');
                    console.log(rawData);
            }
            // BRIAN CUT HERE *END*

        });

        printer.on('end', function() {
            console.log('Fiscal disconnected');
        });

        printer.on('close', function() {
            console.log('Fiscal closed');
        });

        printer.on('timeout', function() {
            console.log('Fiscal timeout');

            var result = {
                success: false,
                message: "Fiscal timeout"
            };

            callback(result);
            return;

            printer.destroy();
        });

        printer.on('error', function(err) {
            console.log('Fiscal error:');
            console.log(err);

            var result = {
                success: false,
                message: err.code
            };

            callback(result);
            printer.destroy();
            return;
        });

        printer.on('connect', function(stream) {

            console.log('Connected to ' + input.printer.type + ' fiscal device at ' + printerOptions.host + ':' + printerOptions.port);

            var my_carrier = carrier.carry(printer);
            my_carrier.on('line',  function(rawData) {

            });

            setTimeout(function() {

                // Initiate Identity request
                var command = '#!#LEN#IQ#';
                var length = ("000" + (command.length + 3).toString(16).toUpperCase()).slice(-3);
                command = Buffer(command.replace('LEN', length));
                command = Buffer.concat([command, Buffer(calculateChecksum(command, 0)), Buffer([0x0d])]);

                console.log('Sending Identity Request ' + command);
                printer.write(command);
            }, printerOptions.timeBetweenCommandsInMs * 0);

            setTimeout(function() {

                // Start receipt
                var command = '#!#LEN#ST#';
                var length = ("000" + (command.length + 3).toString(16).toUpperCase()).slice(-3);
                command = Buffer(command.replace('LEN', length));
                command = Buffer.concat([command, Buffer(calculateChecksum(command, 0)), Buffer([0x0d])]);

                console.log('Sending Start Receipt' + command);
                printer.write(command);

            }, printerOptions.timeBetweenCommandsInMs * 1);
            // Receive ACK

            // OPTIONAL
            // Loop each receipt items (optional)
            // Receive ACK

            // OPTIONAL
            // Payment information (optional)
            // Receive ACK

            setTimeout(function() {

                // Receipt Header
                var checkType;
                switch(input.check.type) {
                    case 'create':
                        checkType = 'normal';
                        break;
                    case 'training':
                        checkType = 'ovning';
                        break;
                    case 'copy':
                        checkType = 'kopia';
                        break;
                    case 'proforma':
                        checkType = 'profo';
                        break;
                    default:
                        var result = {
                            success: false,
                            message: 'Invalid check type given: ' + input.check.type + ' (should be "create" or "training")'
                        };
                        printer.destroy();
                        return callback(result);
                        break;
                }
                var date = new Date(input.check.date);
                date = date.toISOString().replace(/-/g, '').replace('T', '').replace(/:/g, '').substr(0, 12); // '201406011200'
                var receiptId = input.check.id; // Max 12 input.check.id
                var posId = input.printer.options.terminal || input.check.terminal; // Terminal can be overridden in options, defaults to check
                var cashierId = input.check.cashier; // Max 16 input.check.cashier
                var organizationNumber = input.printer.options.organizationNumber; // Max 10
                var receiptTotal = input.check.total;
                var receiptNegativeTotal = input.check.totalDiscount;

                // Array to hold values for VAT 1 - 4
                var taxArray = [
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''},
                    {name: '', percent: '', total: '', vatId: ''}
                ];
                input.check.tax.forEach(function(tax) {
                    if(tax.vatId == "1" || tax.vatId == "2" || tax.vatId == "3" || tax.vatId == "4") {
                        taxArray[parseInt(tax.vatId) - 1] = tax;
                    } else {
                        var result = {
                            success: false,
                            message: 'Invalid VAT ID given: ' + tax.vatId + ' (should be "1", "2", "3" or "4")'
                        };
                        printer.destroy();
                        return callback(result);
                    }
                });

                var vat1 = (taxArray[0].total != '') ? taxArray[0].percent + ';' + taxArray[0].total : ' ';
                var vat2 = (taxArray[1].total != '') ? taxArray[1].percent + ';' + taxArray[1].total : ' ';
                var vat3 = (taxArray[2].total != '') ? taxArray[2].percent + ';' + taxArray[2].total : ' ';
                var vat4 = (taxArray[3].total != '') ? taxArray[3].percent + ';' + taxArray[3].total : ' ';

                var command = '#!#LEN#RH#' + date + '#' + receiptId + '#' + posId + '#' + cashierId + '# #' + organizationNumber + '#' + receiptTotal + '#' + receiptNegativeTotal + '#' + checkType + '#' + vat1 + '#' + vat2 + '#' + vat3 + '#' + vat4 + '#'; // + '60#1#'; // 60 is line width, 1 is add CR to break the line; Removed, not necessary, causing error?
                var length = ("000" + (command.length + 3).toString(16).toUpperCase()).slice(-3);
                command = Buffer(command.replace('LEN', length));
                command = Buffer.concat([command, Buffer(calculateChecksum(command, 0)), Buffer([0x0d])]);

                console.log('Sending Receipt Header ' + command);
                console.log(command);
                printer.write(command);
            }, printerOptions.timeBetweenCommandsInMs * 2);
            // Receive ACK

            setTimeout(function() {

                if (input.check.type == "create" || input.check.type == "copy") //SQ commands are only valid in these modes
                {
                    // Signature request
                    var command = '#!#LEN#SQ#';
                    var length = ("000" + (command.length + 3).toString(16).toUpperCase()).slice(-3);
                    command = Buffer(command.replace('LEN', length));
                    command = Buffer.concat([command, Buffer(calculateChecksum(command, 0)), Buffer([0x0d])]);

                    console.log('Sending Signature Request' + command);
                    printer.write(command);
                }
                else
                {
                    console.log('Sending Signature Request skipped - must be in "normal" or "kopia" mode');
                    var result = {
                        success: true,
                        message: "",
                        serialCode: '',
                        receiptCode: '',
                        organizationNumber: printerOptions.organizationNumber
                    };

                    console.log('Closing fiscal');
                    printer.destroy();

                    callback(null, result);

                }
            }, printerOptions.timeBetweenCommandsInMs * 7); // Added a little delay to ensure CLEANCASH responds from previous RH
            // Receive ACK

            if(printerOptions.testing === true) {
                setTimeout(function() {
                    var result = {
                        success: true,
                        message: "",
                        serialCode: '1234567890',
                        receiptCode: '12312312312312312312123123123123123123121231231231231231231',
                        organizationNumber: printerOptions.organizationNumber
                    };

                    console.log('Closing fiscal');
                    printer.destroy();

                    callback(null, result);

                }, printerOptions.timeBetweenCommandsInMs * 8);
            }

        });
    }

    if(input.printer.type == 'posnet') {

        printerOptions = {
            port: input.printer.options.port,
            host: input.printer.options.host,
            timeout: input.printer.options.timeout || 2000,
            testing: input.printer.options.testing || false,
            timeBetweenCommandsInMs: input.printer.options.timeBetweenCommandsInMs || 200
        }

        var printer = net.connect(printerOptions);
        printer.setTimeout(printerOptions.timeout);

        printer.on('data', function(data) {
            console.log('Received from fiscal:');
            console.log(data);
        });

        printer.on('end', function() {
            console.log('Fiscal disconnected');
        });

        printer.on('close', function() {
            console.log('Fiscal closed');
        });

        printer.on('timeout', function() {
            console.log('Fiscal timeout');

            printer.destroy();

            var result = {
                success: false,
                message: "Fiscal timeout"
            };

            callback(result);
            return;
        });

        printer.on('error', function(err) {
            console.log('Fiscal error:');
            console.log(err);

            printer.destroy();

            var result = {
                success: false,
                message: err.code
            };

            callback(result);
            return;
        });

        printer.on('reconnect', function() {
            console.log('Fiscal reconnected');
        });

        printer.on('connect', function(stream) {
            console.log('Connected to ' + input.printer.type + ' fiscal device at ' + printerOptions.host + ':' + printerOptions.port);

            return printNewCheckPoland(input, printer, callback);

        });
    }
}

exports.openDrawer = function openDrawer(input, callback) {

    if(input.printer.type == 'posnet') {

        printerOptions = {
            port: input.printer.options.port,
            host: input.printer.options.host,
            timeout: input.printer.options.timeout || 2000,
            testing: input.printer.options.testing || false,
            timeBetweenCommandsInMs: input.printer.options.timeBetweenCommandsInMs || 200
        }

        var printer = net.connect(printerOptions);
        printer.setTimeout(printerOptions.timeout);

        printer.on('data', function(data) {
            console.log('Received from fiscal:');
            console.log(data);
        });

        printer.on('end', function() {
            console.log('Fiscal disconnected');
        });

        printer.on('close', function() {
            console.log('Fiscal closed');
        });

        printer.on('timeout', function() {
            console.log('Fiscal timeout');

            printer.destroy();

            var result = {
                success: false,
                message: "Fiscal timeout"
            };

            callback(result);
            return;
        });

        printer.on('error', function(err) {
            console.log('Fiscal error:');
            console.log(err);

            printer.destroy();

            var result = {
                success: false,
                message: err.code
            };

            callback(result);
            return;
        });

        printer.on('reconnect', function() {
            console.log('Fiscal reconnected');
        });

        printer.on('connect', function(stream) {
            // Open drawer
            console.log('Opening drawer');
            printer.write(Buffer.concat([escp, Buffer('1$d'), escslash]));

            console.log('Closing fiscal');
            printer.destroy();

            var result = {
                success: true,
                message: ""
            };

            console.log('Sending success');
            console.log(result);

            callback(null, result);

        });
    }


}

function printNewCheckPoland(input, printer, callback) {

    var my_carrier = carrier.carry(printer, null, null, /\x1b\\/);
    my_carrier.on('line',  function(rawData) {
        var matches = rawData.toString().match(/\x1bP(\d+)#Z([$|#][a-zA-Z])/);

        // We have an error if Match 1 is not 0
        if(matches !== null) {
            if(matches[1] == "0") {
                console.log('Success received for command ' + matches[2]);
                return;
            }

            console.log('Error received!');

            // Cancel transaction
            var command = Buffer('$e0');
            var checksum = Buffer(calculateChecksum(command));
            console.log('Canceling transaction');
            console.log(command);
            printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]));

            // Close fiscal printer
            console.log('Closing fiscal');
            printer.destroy();

            // Send error message
            var result = {
                success: false,
                message: 'Error #' + matches[1] + ' received for command ' + matches[2]
            };

            console.log('Sending error');
            console.log(result);

            callback(null, result);
        }
    });

    var checkHasDiscount = false;

    /*// Cancel transaction (just a test)
     setTimeout(function() {
     var command = Buffer('$e0');
     var checksum = Buffer(calculateChecksum(command));
     console.log('Canceling transaction');
     console.log(command);
     printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]));
     }, printerOptions.timeBetweenCommandsInMs * 0);*/

    var checkType;
    switch(input.check.type) {
        case 'create':
            checkType = 'create';
            break;
        default:
            var result = {
                success: false,
                message: 'Invalid check type given: ' + input.check.type + ' (should be "create")'
            };
            printer.destroy();
            return callback(result);
            break;
    }

    // Set printer error mode
    setTimeout(function() {
        var command = Buffer('3#e');
        var checksum = Buffer(calculateChecksum(command));
        console.log('Setting error mode');
        console.log(command);
        printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]))
    }, printerOptions.timeBetweenCommandsInMs * 0);

    // Set start transaction with number of line items
    setTimeout(function() {
        var command = Buffer(input.check.lineItems.length + '$h');
        var checksum = Buffer(calculateChecksum(command));
        console.log('Sending start transation with ' + input.check.lineItems.length + ' line item(s)');
        console.log(command);
        printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]))
    }, printerOptions.timeBetweenCommandsInMs * 1);

    // Send line items
    input.check.lineItems.forEach(function(item, i) {


        setTimeout(function() {
            var lineItemNumber = Buffer((i+1).toString(10));
            if(typeof item.discount !== 'undefined' && item.discount !== '0,00') {
                checkHasDiscount = true;
                var command = Buffer.concat([lineItemNumber, Buffer(';1;1$l'), Buffer(item.name), cr, Buffer(item.quantity.toString()), cr, Buffer(item.taxName + '/' + item.price + '/' + item.totalBeforeDiscount + '/' + item.discount + '/'), cr]);
                var checksum = Buffer(calculateChecksum(command));
            } else {
                var command = Buffer.concat([lineItemNumber, Buffer('$l'), Buffer(item.name), cr, Buffer(item.quantity.toString()), cr, Buffer(item.taxName + '/' + item.price + '/' + item.total + '/')]);
                var checksum = Buffer(calculateChecksum(command));
            }
            console.log('Sending item: ' + item.name);
            console.log(item);
            console.log(command);
            console.log(command.toString());
            printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]))
        }, printerOptions.timeBetweenCommandsInMs * (i+2));
    });

    var commandNumber = input.check.lineItems.length + 2;

    // Send total line, finalizing transaction
    setTimeout(function() {
        var cashier = input.check.terminal.substr(-1) + input.check.cashier.substr(0, 2);

        if(input.check.checkDiscount !== '0,00') {
            var command = Buffer.concat([Buffer('1;0;0;0;3;1$e' + cashier), cr, Buffer('/' + input.check.total + '/' + input.check.checkDiscount + '/')]);
            var checksum = Buffer(calculateChecksum(command));
        } else {
            var command = Buffer.concat([Buffer('1;0;0;0;0;1$e' + cashier), cr, Buffer('/' + input.check.total + '//')]);
            var checksum = Buffer(calculateChecksum(command));
        }
        console.log('Sending total line, finalizing transaction');
        console.log(command);
        console.log(command.toString());
        printer.write(Buffer.concat([escp, Buffer.concat([command, checksum]), escslash]))
    }, printerOptions.timeBetweenCommandsInMs * commandNumber);

    // Open drawer TODO -- call class method instead
    setTimeout(function() {
        console.log('Opening drawer');
        printer.write(Buffer.concat([escp, Buffer('1$d'), escslash]))
    }, printerOptions.timeBetweenCommandsInMs * (commandNumber+1));

    // Close fiscal connection
    setTimeout(function() {
        console.log('Closing fiscal');
        printer.destroy();

        var result = {
            success: true,
            message: ""
        };

        console.log('Sending success');
        console.log(result);

        callback(null, result);
    }, printerOptions.timeBetweenCommandsInMs * (commandNumber+2));


}

function calculateChecksum(buffer, startingChar) {
    result = (typeof startingChar !== 'undefined') ? startingChar : 0xff;
    for(var i = 0; i < buffer.length; i++) {
        result = result ^ buffer[i];
    }
    var res = result.toString(16).toUpperCase();
    return res;
}