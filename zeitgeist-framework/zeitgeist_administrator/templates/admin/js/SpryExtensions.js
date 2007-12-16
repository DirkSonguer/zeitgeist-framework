
		var paginationOffset = 0;
		var paginationSize = 10;
		var paginationStop = paginationOffset + paginationSize;

		function filterSpryDataset(filterRow, filterParameter, spryDataset)
		{
			doFilterFunc = function(dataSet, row, rowNumber)
			{
				if (filterParameter == -1)
				{
					var regExp = new RegExp("(.*)", "i");
				}
				else
				{
					filterString = "^"+filterParameter+"";
					var regExp = new RegExp(filterString, "i");
				}
	
				if (row[filterRow].search(regExp) != -1)
				{
					return row;                     // Return the row to keep it in the data set.
				}
				else
				{
					return null;                    // Return null to remove the row from the data set.
				}
			}
			
			spryDataset.filter(doFilterFunc);
		}
		
		function showPaginationInfo(infoElement, spryDataset)
		{
			var numRows = spryDataset.getUnfilteredData().length;	

			if (numRows > 0)
			{
				if (numRows < paginationStop)
				{
					end = numRows;
				}
				else
				{
					end = paginationStop
				}
				
				if (paginationOffset == 0)
				{
					start = 1;
				}
				else
				{
					start = paginationOffset;
				}
				
				document.getElementById(infoElement).innerHTML = '<p style="text-align:center;"><strong>' + start + ' - ' + end + ' (of ' + numRows + ')</strong></p>';
			}
		}
		

		function updatePagination(offset, spryDataset)
		{
			var numRows = spryDataset.getUnfilteredData().length;
			
			if (offset > (numRows - paginationSize))
				offset = numRows - paginationSize;
			if (offset < 0)
				offset = 0;
		
			paginationOffset = offset;
			paginationStop = offset + paginationSize;
		
			spryDataset.filter(paginationFunc);
		}		