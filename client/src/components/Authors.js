import React from 'react'
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'

class Authors extends React.Component {

	render() {    
		return (
			<WithDefaults>
			{(locs) => (
        		<div>
	  				<PageHeader>{locs.authors}</PageHeader>	
	         	</div>
	        )}
	        </WithDefaults>
	    )
	}

}

export default Authors;
