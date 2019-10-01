import React from 'react';
import PropTypes from 'prop-types';

export default class Row extends React.Component {
    render() {
        return (
            <tr>
                <td>here is a row :D</td>
            </tr>
        );
    }
}

Row.propTypes = {
    featureDefinition: PropTypes.shape({
        identifier: PropTypes.string,
    }).isRequired
};
