import React from 'react';
import {PageHeader} from 'react-bootstrap'
import {Locs} from '../l10n'


class Titles extends React.Component {

	render() {    
		return (
        	<div>
  				<PageHeader>Titles</PageHeader>		
        		<p>{window.navigator.userAgent}</p>
         	</div>
		)
	}

}

export default Titles;
