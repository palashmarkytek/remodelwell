<?php defined('BASEPATH') or exit('No direct script access allowed');

class Vertical_model extends CI_Model
{
    public function get_all()
    {
        // UPDATED: Newest locally saved verticals are shown first.
        return $this->db->order_by('vertical_name', 'ASC')->get('ci_verticals')->result_array();
    }

    public function exists($id)
    {
        // UPDATED: Confirm the local vertical record before changing its status.
        return $this->db
            ->where('id', (int) $id)
            ->limit(1)
            ->count_all_results('ci_verticals') > 0;
    }

    public function change_status($id, $is_active)
    {
        // UPDATED: Update only is_active; synced Leadspedia values remain untouched.
        return $this->db
            ->where('id', (int) $id)
            ->update('ci_verticals', array('is_active' => ((int) $is_active === 1 ? 1 : 0)));
    }

    public function update_price($id, $price)
    {
        // UPDATED: Update only the price column from the inline listing editor.
        // No other vertical data or existing flow is changed.
        return $this->db
            ->where('id', (int) $id)
            ->update('ci_verticals', array('price' => $price));
    }

    public function insert_new_only($rows)
    {
        // UPDATED: Insert new verticals and refresh only Leadspedia metadata for existing rows.
        // Existing local status/is_active and the current application flow remain unchanged.
        $inserted = 0;
        $updated = 0;

        foreach ((array) $rows as $row) {
            $existing = $this->db
                ->select('id, vertical_id')
                ->where('vertical_id', $row['vertical_id'])
                ->limit(1)
                ->get('ci_verticals')
                ->row_array();

            if (!empty($existing)) {
                // UPDATED: Populate the new API metadata columns for records already synced earlier.
                $metadata = array(
                    'group_id' => $row['group_id'],
                    'group_name' => $row['group_name'],
                    'leadspedia_created_on' => $row['leadspedia_created_on'],
                    'total_offers' => $row['total_offers'],
                    'raw_data' => $row['raw_data']
                );

                if ($this->db
                    ->where('id', (int) $existing['id'])
                    ->update('ci_verticals', $metadata)) {
                    $updated++;
                }

                continue;
            }

            // Existing behavior: newly synced verticals are active by default.
            $row['is_active'] = 1;
            $row['created_at'] = date('Y-m-d H:i:s');

            if ($this->db->insert('ci_verticals', $row)) {
                $inserted++;
            }
        }

        return array(
            'inserted' => $inserted,
            'updated' => $updated,
            'total' => count((array) $rows)
        );
    }}
