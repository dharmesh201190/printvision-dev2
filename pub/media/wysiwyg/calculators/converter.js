(function (root) {
	var table = {};

	var unitConverter = function (value, unit) {
		this.value = value;
		this.computed_value = null;
		this.history = [];
		this._set_format = null;
		if (unit) {
			this.currentUnit = unit;
		}
	};

	unitConverter.prototype.as = function (targetUnit) {
		// this allows for the ability to chain conversions...
		// I'm lazy, I don't want to even write a for loop with a table of
		// conversion factors, just chain from known conversion to it's counter part
		if (this.computed_value !== null) {
			this.history.push({
				'unit': this.currentUnit,
				'target': this.targetUnit,
				'value': this.value,
				'computed': this.computed_value
			});
			this.currentUnit = this.targetUnit;
			this.targetUnit = targetUnit;
			this.value = this.computed_value;
			this.convert();
		} else {
			this.targetUnit = targetUnit;
			this.convert();
		}
		return this;
	};
	unitConverter.prototype.is = function (currentUnit) {
		this.currentUnit = currentUnit;
		return this;
	};

	unitConverter.prototype.setFormat = function(callback) {
		if (typeof callback === 'function') {
			this._set_format = callback;
		}
	};
	unitConverter.prototype.format = function() {

	};

	unitConverter.prototype.convert = function() {
		var target = table[this.targetUnit];
		var current = table[this.currentUnit];
		var m, s;
		if (target.base != current.base) {
			throw new Error('Incompatible units; cannot convert from "' + this.currentUnit + '" to "' + this.targetUnit + '"');
		}
		m = this.maxDecimalLength([this.value, current.multiplier, target.multiplier]);
		// coerced into integers
		this.computed_value = (this.value * m) * ((current.multiplier * m)) / (target.multiplier * m);
		this.computed_value  = this.computed_value / m;
		return this;
	};

	unitConverter.prototype.maxDecimalLength = function(args) {
		var highest = 0, s, m;
		for (var i = 0; i < args.length; i++) {
			var arg = args[i];
			if (arg % 1 !== 0) {
				// we have a float
				s = arg.toString().split('.');
				// IF -e is found
				if (/e-\d/.test(s[1])) {
					var t = s[1].split('-');
					m = parseInt(t[1]) + 2;
				} else {
					m = s[1].length;
				}
				if (m > highest) {
					highest = m;
				}
			}
		}
		if (highest == 0) {
			return 1;
		} else {
			return Math.pow(10, highest);
		}
	};

	unitConverter.prototype.val = function() {
		// first, convert from the current value to the base unit
		return this.computed_value;
	};

	unitConverter.prototype.toString = function() {
		return this.val() + ' ' + this.targetUnit;
	};

	unitConverter.prototype.debug = function() {
		if (this.history.length == 0) {
			return this.value + ' ' + this.currentUnit + ' is ' + this.val() + ' ' + this.targetUnit;
		}
		return this.history;
	};

	/**
	 * Unit Conversion
	 *
	 * @param siUnitSymbol
	 * @param actualUnit
	 * @param multiplier
	 */
	unitConverter.addUnit = function (siUnitSymbol, actualUnit, multiplier) {
		if (Array.isArray(actualUnit)) {
			for (var i = 0; i < actualUnit.length; i++) {
				table[actualUnit[i]] = { base: siUnitSymbol, actual: actualUnit, multiplier: multiplier };
			}
		} else {
			table[actualUnit] = { base: siUnitSymbol, actual: actualUnit, multiplier: multiplier };
		}
	};

	var prefixes = {
		'Y':  'yotta',
		'Z':  'zetta',
		'E':  'exa',
		'P':  'peta',
		'T':  'tera',
		'G':  'giga',
		'M':  'mega',
		'k':  'kilo',
		'h':  'hecto',
		'da': 'deka',
		'':   false,
		'd':  'deci',
		'c':  'centi',
		'm':  'milli',
		'u':  'micro',
		'n':  'nano',
		'p':  'pico',
		'f':  'femto',
		'a':  'atto',
		'z':  'zepto',
		'y':  'yocto'
	};

	var factors = [24, 21, 18, 15, 12, 9, 6, 3, 2, 1, 0, -1, -2, -3, -6, -9, -12, -15, -18, -21, -24];
	// SI units only, that follow the mg/kg/dg/cg type of format
	var units = {
		'g': ['gram', 'grams'],
		't': ['tesla','teslas'],
		'l': ['liter','litre','liters','litres'],
		'm': ['meter','metre','meters','metres']
	};

	for (var j in units) {
		var all_units = [];
		if (units.hasOwnProperty(j)) {
			all_units.push(j);
			for (var k = 0; k < units[j].length; k++) {
				all_units.push(units[j][k]);
			}
			var i = 0;
			for (var prefix in prefixes) {
				if (prefixes.hasOwnProperty(prefix)) {
					for (k = 0; k < all_units.length; k++) {
						var base = all_units[k];
						unitConverter.addUnit(j, prefixes[prefix] + base, Math.pow(10, factors[i]));
						unitConverter.addUnit(j, prefix + base, Math.pow(10, factors[i]));
					}
					i++;
				}
			}
		}
	}

	// we use the SI gram unit as the base; this allows
	// us to convert between SI and English units
	unitConverter.addUnit('g', ['metric ton', 'tonne', 'tonnes', 'metric tons'], 1000000);
	unitConverter.addUnit('g', ['grain', 'grains'], 0.06479891);
	unitConverter.addUnit('g', ['drachm'], 1.7718451953125);
	unitConverter.addUnit('g', ['ounces', 'oz', 'ounce'], 28.3495231);
	unitConverter.addUnit('g', ['pounds','lb','pound'], 453.59237);
	unitConverter.addUnit('g', ['stone', 'stones'], 6350.29318);
	unitConverter.addUnit('g', ['quarter', 'quarters'], 12700.58636);
	unitConverter.addUnit('g', ['hundredweight', 'hundredweights'], 50802.34544);
	unitConverter.addUnit('g', ['ton', 'tons'], 1016046.9088);
	unitConverter.addUnit('m', ['foot', 'feet', 'ft'], 0.3048);
	unitConverter.addUnit('m', ['yards','yard','yrd'], 0.9144);
	unitConverter.addUnit('m', ['inches','inch','in'], 0.0254);
	unitConverter.addUnit('m', ['microinch','uin','min', 'microinches'], .0000000254);
	unitConverter.addUnit('l', ['floz','fluid ounce','fluid ounces'], 0.028413);
	unitConverter.addUnit('l', ['gill','gills'], 0.028413);
	unitConverter.addUnit('l', ['pint','pints'], 0.028413);
	unitConverter.addUnit('l', ['quart','qrt','quarts'], 0.028413);
	unitConverter.addUnit('l', ['gallon','gallons'], 0.028413);
	unitConverter.addUnit('l', ['peck'],9.09218);
	unitConverter.addUnit('l', ['kenning','bucket'], 18.18436);
	unitConverter.addUnit('l', ['bushel'], 36.36872);
	unitConverter.addUnit('l', ['strike'], 72.73744);
	unitConverter.addUnit('l', ['quarter','pail'], 290.94976);
	unitConverter.addUnit('l', ['chaldron'], 1163.79904);
	unitConverter.addUnit('l', ['last'], 2909.4976);
	unitConverter.addUnit('l', ['firkin'], 40.91481);
	unitConverter.addUnit('l', ['kilderkin'], 81.82962);
	unitConverter.addUnit('l', ['barrel'], 163.65924);
	unitConverter.addUnit('l', ['hogshead'], 245.48886);

	root.$u = function (value, unit) {
		return new unitConverter(value, unit);
	};
})(this);
