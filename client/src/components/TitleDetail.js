import React from 'react';
import {PageHeader} from 'react-bootstrap'
import WithDefaults from './WithDefaults'

class TitleDetail extends React.Component {
	constructor() {
	    super()
	    this.state = {
	    }
  	}

  	render() {
  		return (
        <WithDefaults>
          {(locs) => (
      			<div>
      				<PageHeader>TitleDetail</PageHeader>		
      			</div>
          )}
        </WithDefaults>
  		)
  	}
}

export default TitleDetail
