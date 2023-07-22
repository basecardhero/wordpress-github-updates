/**
 * Example javascript test file.
 *
 * @since 0.1.0
 */

import { someRandomFunction } from './functions.js';

describe( 'someRandomFunction', () => {
	it( 'will return the string', () => {
		expect( someRandomFunction() ).toBe( 'Some random text...' );
	} );
} );
